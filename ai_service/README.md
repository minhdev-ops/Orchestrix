# AgriVerse AI Inference Service (Enhanced)

Microservice **FastAPI** phục vụ inference cho model `best_vit.keras` (Vision Transformer, 71 classes bệnh/thực vật), được Laravel module AgriVerse gọi qua HTTP khi AI_PLANT_DOCTOR_PROVIDER=vit_local.

## 🔷 Enhanced Pipeline: Grounding DINO → Crop → Keras Disease Diagnosis

```
Laravel PlantDoctor API ──HTTP──> FastAPI :8501 ──> best_vit.keras
        │                                    │     (ViT 71 classes)
        │               (auto_detect=true)    ▼
        │        ┌────────────────────┐
        │        │   /detect-leaves   │ ←─── Grounding DINO
        │        │ (Find all leaf bbox)│
        │        └────────────────────┘
        │                  │
        │         ┌────────▼────────┐
        │         │   /crop x N      │ ← Crop each leaf
        │         └─────────────────┘
        │                  │
        │         ┌────────▼────────┐
        │         │ /predict-leaf x N│ ← Ensembled prediction per leaf
        │         └─────────────────┘
        │                  │
        │         ┌────────▼────────┐
        │         │ Aggregate votes │ ← Major voting + confidence avg
        │         └─────────────────┘
        │                  │
        └──────────────────┘
(auth + DB + history)     (New endpoints for full pipeline)
```

## 1. Cấu trúc

```
ai_service/
├── app/
│   ├── main.py            FastAPI app (5+ endpoints now)
│   ├── model_loader.py    Singleton load model + strip augmentation + device
│   └── config.py          Settings từ env (tiền tố AI_)
├── scripts/
│   ├── predict_cli.py     Test  ảnh qua model (không qua HTTP)
│   └── verify_labels.py   Kiểm chứng labels.txt khớp output index của model
├── labels.txt             71 nhãn (theo thứ tự C-locale alphabetical)
├── requirements.txt
└── Dockerfile
```

Model & labels được mount bind-mount vào container (không copy into image) → đổi model chỉ cần thay file trên host, không cần build lại image.

## 2. Chạy local (không Docker)

Yêu cầu: Python 3.11+, có file best_vit.keras ở root project.

```bash
cd ai_service
python -m venv .venv && source .venv/bin/activate
pip install -r requirements.txt

# Các env mặc định (có thể sửa trong .env file):
#   AI_MODEL_PATH=/path/to/best_vit.keras
#   AI_LABELS_PATH=./labels.txt
#   AI_SERVICE_PORT=8501
uvicorn app.main:app --host 0.0.0.0 --port 8501 --reload
```

Test nhanh CLI:

```bash
python scripts/predict_cli.py --image /path/to/any_plant_photo.jpg
```

## 3. Chạy bằng Docker (cùng stack AgriVerse)

Từ root project:

```bash
docker compose up -d ai_service
docker compose logs -f ai_service       # đợi "Model đã load"
curl http://localhost:8501/health
```

Trong mạng Docker, Laravel gọi service bằng http://ai_service:8501 (đã được set mặc định qua AI_PLANT_DOCTOR_VIT_SERVICE_URL).

## 4. API endpoints

Tất cả trừ `/health` yêu cầu header Authorization: Bearer <token> nếu AI_AUTH_TOKEN được set (rỗng = bỏ qua).

### GET /health

```json
{
  "status": "ready",
  "service": "agriverse-vit-inference",
  "version": "0.1.0",
  "model_loaded": true,
  "device": "CPU",
  "input_size": 224,
  "top_k_default": 5
}
```

### GET /labels

```json
{ "count": 71, "labels": ["Algal Leaf Spot (Jackfruit)", "...", "healthy (Tomato)"] }
```

### POST /detect-leaves (NEW -增強功能)

Phát hiện tất cả bbox lá trong ảnh using Grounding DINO.

Request: `multipart/form-data field "file"`

Response:
```json
{
  "image_size": {"width": 800, "height": 600},
  "leaf_count": 3,
  "bboxes": [
    {"xmin": 10, "ymin": 20, "xmax": 150, "ymax": 200},
    {"xmin": 200, "ymin": 50, "xmax": 350, "ymax": 250},
    {"xmin": 50, "ymin": 300, "xmax": 200, "ymax": 450}
  ],
  "method": "grounding_dino"
}
```

### POST /crop (NEW -增強功能)

Tr出一片 leaf từ ảnh gốc theo bbox.

Query params: `bbox={xmin:10,ymin:20,xmax:150,ymax:200}` (JSON string)

Response:
```json
{
  "status": "ok",
  "cropped_size": {"width": 224, "height": 224},
  "original_bbox": {"xmin": 10, "ymin": 20, ...},
  "image_hex": "a1b2c3..."
}
```

### POST /predict-leaf (NEW -增強功能)

Dự đoán bệnh cho một lá cây riêng lẻ (ảnh đã crop).

Request: `multipart/form-data field "file"` (leaf image)

Response giống như `/predict` nhưng cho 1 leaf duy nhất.

### POST /predict (Enhanced mode)

Predict directly on full image (backward compatible, auto_detect=false).

```bash
curl -X POST http://localhost:8501/predict?top_k=3&auto_detect=false \
     -H "Authorization: Bearer $AI_PLANT_DOCTOR_VIT_TOKEN" \
     -F "file=@leaf.jpg"
```

### POST /predict (Full enhanced pipeline - default)

When auto_detect=true (default), service automatically detects leaves, crops each, predicts, and aggregates results using major voting.

```bash
curl -X POST http://localhost:8501/predict?top_k=3 \
     -H "Authorization: Bearer $AI_PLANT_DOCTOR_VIT_TOKEN" \
     -F "file=@plant_with_multiple_leaves.jpg"
```

Response includes ensemble info:
```json
{
  "predicted_label": "Bacterial spot (Tomato)",
  "confidence": 0.823456,
  "leaf_count": 3,
  "dominant_vote": "Bacterial spot (Tomato) (2/3 leaves)",
  "top_k_aggregated": [
    {"label": "Bacterial spot (Tomato)", "avg_confidence": 0.85, "votes": 2},
    {"label": "Early blight (Tomato)", "avg_confidence": 0.65, "votes": 1}
  ],
  "all_leaf_predictions": [...],
  "method": "ensemble_with_leaf_detection",
  "inference_ms": 1234.56
}
```

Swagger UI: http://localhost:8501/docs

## 5. Cấu hình (env)

| Env                       | Mặc định                          | Mô tả |
|---------------------------|-----------------------------------|-------|
| `AI_MODEL_PATH`           | `/models/best_vit.keras`          | Đường dẫn file model |
| `AI_LABELS_PATH`          | `/app/labels.txt`                 | File nhãn, 1 dòng/nhãn |
| `AI_INPUT_SIZE`           | `224`                             | ViT input size |
| `AI_TOP_K`                | `5`                               | Số top-k mặc định |
| `AI_MAX_IMAGE_MB`         | `10`                              | Giới hạn dung lượng ảnh upload |
| `AI_DEVICE`               | `auto`                            | `auto` / `cpu` / `gpu` |
| `AI_STRIP_AUGMENTATION`  | `false`                           | Bỏ hẳn layer augmentation khỏi model. Mặc định false vì predict() đã set training=False. |
| `AI_AUTH_TOKEN`           | (rỗng)                            | Token bảo vệ endpoint. Rỗng = tắt |
| `AI_SERVICE_PORT`         | `8500`                            | Port service FastAPI |

###參數增強 (New for Enhanced Pipeline)

| Env                        | Mặc định   | Mô tả |
|----------------------------|------------|-------|
| `AI_VIT_AUTO_DETECT`       | `true`     | Nếu true: tự động detect leaves before predict. false → predict direct (back compat) |
| `AI_VIT_MIN_CONFIDENCE`    | `0.45`     | Ngưỡng min confidence, dưới này cảnh báo người dùng chụp lại ảnh |

## 6. Quan trọng: thứ tự labels.txt

File labels.txt có sẵn trong repo (71 nhãn), được sinh từ thư mục train gốc theo thứ tự C-locale alphabetical - đúng với thứ序 mà image_dataset_from_directory của Keras mac dinh dung, hop voi output index 0..70 cua model.

Service chi can 2 thu: best_vit.keras + labels.txt. Ban KHONG can dataset de chay service.

Neu ban retrain model voi thu tu nhan khac, hay:
  - Tao lai labels bang thu muc train moi (neu ban con dataset), hoac
  - Chinh sua labels bang tay theo index del output cua model, sau do
  - Chay tuy chon scripts/verify_labels.py (neu co dataset) de kiem chung.

## 7. Field "augmentation" trong model

best_vit.keras lưu kèm layer data_augmentation (chỉ hợp lệ lúc train). Mặc định service giữ nguyên model và gọi model.predict() ở chế độ training=False, khiến layer augmentation tự chuyển sang identity → kết quả inference ổn định, không random. Layer Rescaling(1/255) vẫn nằm trong model, nên service không normalize thêm ngoài việc resize(224, 224).

Nếu muốn bỏ hẳn layer augmentation khỏi graph (giảm overhead, đảm bảo 100%), set AI_STRIP_AUGMENTATION=true. Service sẽ rebuild model bỏ layer này khi startup; nếu rebuild thất bại (do khác format giữa version Keras), service giữ nguyên model gốc và log cảnh báo.

## 8. Troubleshooting

- **503 "Model chưa load xong"**: lần đầu load có thể 10-30s. Đợi /health trả "ready".
- **OutOfBounds / shape error**: kiểm AI_INPUT_SIZE=224 đúng với model.
- **Dự đoán sai nhiều**: chạy verify_labels.py. Nếu accuracy thấp, có thể labels.txt lệch → regenerate theo thư mục train đúng thứ tự mà bạn đã train.
- **OOM CPU**: nếu ảnh lớn, resize về 224 trước khi gửi. Layer Resize không nằm trong model.
- **Leaf detection returns empty bbox**: mock Grounding DINO placeholder đang được sử dụng. Thay thế bằng model Grounding DINO thực (from transformers library) để phát hiện lá chính xác.

## 9. Triển khai Grounding DINO thực (TODO)

Thay thế hàm mock_grounding_dino_detection() trong main.py bằng:

```python
from transformers import AutoProcessor, AutoModelForGroundingDINO

processor = AutoProcessor.from_name("IDEA/CCNL-Unicoder-t5-base-grounding-dino")
model = AutoModelForGroundingDINO.from_pretrained("IDEA/CCNL-Unicoder-t5-base-grounding-dino")

def real_grounding_dino_detection(img, query="leaf"):
    # Implement actual object detection for leaves
    # Returns list of bbox dicts [{xmin, ymin, xmax, ymax}, ...]
    pass
```

Yêu cầu thêm: `transformers`, `torch` trong requirements.txt.
