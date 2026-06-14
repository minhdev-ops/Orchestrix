<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Modules\AgriVerse\Models\ThreeDAsset;
use App\Modules\AgriVerse\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use App\Jobs\OptimizeThreeDAsset;

class ThreeDAssetController {
    public function index( Request $request ): AnonymousResourceCollection {
        $assets = ThreeDAsset::with( 'product' )
                             ->when( $request->product_id, fn( $q, $v ) => $q->where( 'product_id', $v ) )
                             ->when( $request->asset_type, fn( $q, $v ) => $q->where( 'asset_type', $v ) )
                             ->latest()
                             ->paginate( $request->per_page ?? 15 );

        return JsonResource::collection( $assets );
    }

    public function show( ThreeDAsset $asset ): JsonResource {
        $asset->load( 'product' );

        return JsonResource::make( $asset );
    }

    public function upload( Request $request ): JsonResource {
        $data = $request->validate( [
            'product_id' => 'required|integer|exists:products,id',
            'file'       => 'required|file|mimes:glb,gltf,obj,fbx,zip,mp4,mov|max:512000',
            'asset_type' => 'required|in:360_view,ar_model,animation',
            'metadata'   => 'nullable|array',
        ] );

        $file     = $request->file( 'file' );
        $ext      = $file->getClientOriginalExtension();
        $filename = uniqid() . '_' . $file->getClientOriginalName();

        // Store original file
        $originalPath = $file->storeAs( '3d-assets/original/' . $data['product_id'], $filename, 'public' );

        $asset = ThreeDAsset::create( [
            'user_id'            => $request->user()->id,
            'product_id'         => $data['product_id'],
            'original_filename'  => $file->getClientOriginalName(),
            'original_path'      => $originalPath,
            'format'             => $ext,
            'asset_type'         => $data['asset_type'],
            'compression_status' => 'pending',
            'file_size'          => $file->getSize(),
            'metadata'           => $data['metadata'] ?? [],
        ] );

        // Dispatch async job to compress/optimize (.glb/.gltf pipeline)
        OptimizeThreeDAsset::dispatch( $asset )->onQueue( 'high' );

        return JsonResource::make( $asset );
    }

    public function model( Product $product ): JsonResource {
        $asset = ThreeDAsset::where( 'product_id', $product->id )
                            ->where( 'compression_status', 'completed' )
                            ->whereIn( 'asset_type', [ '360_view', 'ar_model' ] )
                            ->latest()
                            ->first();

        if ( ! $asset ) {
            abort( 404, 'No optimized 3D model available for this product.' );
        }

        $url = $asset->compressed_path
            ? Storage::url( $asset->compressed_path )
            : Storage::url( $asset->original_path );

        return JsonResource::make( [
            'id'        => $asset->id,
            'type'      => $asset->asset_type,
            'format'    => $asset->format,
            'url'       => $url,
            'file_size' => $asset->compressed_file_size ?? $asset->file_size,
        ] );
    }

    public function arConfig( Product $product ): JsonResource {
        $asset = ThreeDAsset::where( 'product_id', $product->id )
                            ->where( 'asset_type', 'ar_model' )
                            ->where( 'compression_status', 'completed' )
                            ->latest()
                            ->first();

        if ( ! $asset ) {
            abort( 404, 'No AR model available for this product.' );
        }

        $productData = $product->only( [ 'name', 'technical_specs' ] );

        return JsonResource::make( [
            'model_url'       => Storage::url( $asset->compressed_path ?? $asset->original_path ),
            'format'          => $asset->format,
            'scale'           => 1.0,
            'unit'            => 'meter',
            'product_name'    => $product->name,
            'technical_specs' => $product->technical_specs,
        ] );
    }

    public function compress( Request $request, ThreeDAsset $asset ): JsonResource {
        if ( $asset->compression_status === 'completed' ) {
            return JsonResource::make( $asset );
        }

        $asset->update( [ 'compression_status' => 'processing' ] );

        OptimizeThreeDAsset::dispatch( $asset )->onQueue( 'high' );

        return JsonResource::make( $asset->fresh() );
    }

    public function store( Request $request ): JsonResource {
        if ( $request->hasFile( 'file' ) ) {
            return $this->upload( $request );
        }

        $data = $request->validate( [
            'product_id'        => 'required|integer|exists:products,id',
            'original_filename' => 'nullable|string|max:255',
            'original_path'     => 'nullable|string|max:500',
            'format'            => 'nullable|string|max:10',
            'asset_type'        => 'required|in:360_view,ar_model,exploded_view,animation',
            'metadata'          => 'nullable|array',
        ] );

        $data['user_id']            = $request->user()->id;
        $data['compression_status'] = 'pending';

        $asset = ThreeDAsset::create( $data );

        return JsonResource::make( $asset );
    }

    public function update( Request $request, ThreeDAsset $asset ): JsonResource {
        $data = $request->validate( [
            'original_filename'  => 'nullable|string|max:255',
            'compressed_path'    => 'nullable|string|max:500',
            'format'             => 'nullable|string|max:10',
            'asset_type'         => 'nullable|in:360_view,ar_model,animation',
            'compression_status' => 'nullable|in:pending,processing,completed,failed',
            'metadata'           => 'nullable|array',
        ] );

        $asset->update( array_filter( $data, fn( $v ) => ! is_null( $v ) ) );

        return JsonResource::make( $asset );
    }

    public function destroy( ThreeDAsset $asset ) {
        if ( $asset->original_path ) {
            Storage::disk( 'public' )->delete( $asset->original_path );
        }
        if ( $asset->compressed_path ) {
            Storage::disk( 'public' )->delete( $asset->compressed_path );
        }

        $asset->delete();

        return response()->json( [ 'message' => 'Asset deleted.' ] );
    }
}
