<?php

namespace App\Modules\AgriVerse\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\Factory\Psr17Factory;

class AuthBridgeController
{
    public function __construct(
        protected ResourceServer $server,
        protected TokenRepository $tokens,
    ) {}

    public function validateToken(Request $request): JsonResponse
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'valid' => false,
                'error' => 'No bearer token provided',
            ], 401);
        }

        $request->merge(['bearer_token' => $token]);
        $request->validate(['bearer_token' => 'required|string']);

        try {
            $psr = (new Psr17Factory)->createServerRequest('GET', '/');

            foreach ($request->headers->all() as $name => $values) {
                foreach ($values as $value) {
                    $psr = $psr->withHeader($name, $value);
                }
            }

            $psrRequest = $this->server->validateAuthenticatedRequest($psr);
            $tokenId = $psrRequest->getAttribute('oauth_access_token_id');
            $userId = $psrRequest->getAttribute('oauth_user_id');
            $scopes = $psrRequest->getAttribute('oauth_scopes', []);

            $tokenModel = Token::find($tokenId);

            if (! $tokenModel || $tokenModel->revoked) {
                return response()->json([
                    'valid' => false,
                    'error' => 'Token has been revoked',
                ], 401);
            }

            $user = User::find($userId);

            if (! $user || ! $user->is_active) {
                return response()->json([
                    'valid' => false,
                    'error' => 'User not found or inactive',
                ], 401);
            }

            return response()->json([
                'valid' => true,
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                    'is_active' => $user->is_active,
                ],
                'token' => [
                    'id' => $tokenId,
                    'scopes' => $scopes,
                    'expires_at' => $tokenModel->getAttribute('expires_at')?->toIso8601String(),
                ],
            ]);

        } catch (OAuthServerException $e) {
            return response()->json([
                'valid' => false,
                'error' => $e->getMessage(),
            ], 401);
        } catch (\Throwable $e) {
            Log::error('Token validation error: '.$e->getMessage());

            return response()->json([
                'valid' => false,
                'error' => 'Token validation failed',
            ], 500);
        }
    }

    public function publicKey(): JsonResponse
    {
        $keyPath = config('passport.public_key');
        if (! $keyPath) {
            abort(500, 'Passport public key not configured.');
        }

        if (! file_exists($keyPath)) {
            return response()->json([
                'error' => 'Public key not found',
            ], 500);
        }

        $key = file_get_contents($keyPath);

        return response()->json([
            'key' => $key,
            'algorithm' => 'RS256',
        ]);
    }
}
