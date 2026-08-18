"""Knowledge Base module cho plant disease treatment recommendations.

Cung cấp thông tin về:
- Mô tả bệnh
- Biện pháp xử lý (treatment)
- Biện pháp phòng ngừa (prevention)
- Mức độ nghiêm trọng (severity)
"""
from __future__ import annotations

import logging
from dataclasses import dataclass, field
from typing import Dict, List, Optional, Any

logger = logging.getLogger("agriverse_knowledge")


@dataclass
class DiseaseInfo:
    """Thông tin về một bệnh cây."""
    name: str
    plant: str
    vietnamese_name: str
    description: str
    treatments: List[str]
    prevention: List[str]
    severity_factors: List[str]  # Các yếu tố ảnh hưởng đến severity

    def to_dict(self) -> Dict[str, Any]:
        return {
            "name": self.name,
            "plant": self.plant,
            "vietnamese_name": self.vietnamese_name,
            "description": self.description,
            "treatments": self.treatments,
            "prevention": self.prevention,
            "severity_factors": self.severity_factors,
        }


# ---------------------------------------------------------------------------
# Knowledge Base Data
# ---------------------------------------------------------------------------

DISEASE_DATABASE: Dict[str, DiseaseInfo] = {
    # === bacterial diseases ===
    "Bacterial spot": DiseaseInfo(
        name="Bacterial spot",
        plant="Tomato/Pepper",
        vietnamese_name="Đốm vi khuẩn",
        description="Bệnh do vi khuẩn Xanthomonas gây ra, tạo các đốm nước nhỏ trên lá, "
                    "sau đó chuyển thành đốm nâu với viền vàng. Có thể ảnh hưởng đến cả quả.",
        treatments=[
            "Phun thuốc chứa đồng (Copper-based) như Bordeaux mixture",
            "Sử dụng Streptomycin hoặc Oxytetracycline theo hướng dẫn",
            "Loại bỏ lá và quả bị bệnh nặng",
            "Tưới nước gốc, tránh tưới lên lá",
        ],
        prevention=[
            "Sử dụng hạt giống đã qua xử lý nhiệt",
            "Luân canh crops, không trồng cùng họ hàng 2-3 năm",
            "Đảm bảo khoảng cách giữa các cây để thông thoáng",
            "Tránh tưới nước bằng phương pháp phun mưa",
        ],
        severity_factors=["Độ ẩm cao", "Nhiệt độ 25-30°C", "Gió mạnh lan truyền vi khuẩn"],
    ),

    "Bacterial Blight": DiseaseInfo(
        name="Bacterial Blight",
        plant="Cotton",
        vietnamese_name="Bệnh cháy lá vi khuẩn",
        description="Bệnh do vi khuẩn Xanthomonas citri pv. malvacearum gây ra. "
                    "Gây cháy lá,腐烂 bông, giảm năng suất nghiêm trọng.",
        treatments=[
            "Phun thuốc chứa đồng (Copper oxychloride)",
            "Sử dụng antibiotic như Kasugamycin",
            "Loại bỏ cây bệnh nặng",
            "Khử trùng dụng cụ làm việc",
        ],
        prevention=[
            "Sử dụng giống kháng bệnh",
            "Xử lý hạt giống bằng acid sulfuric",
            "Luân canh crops",
            "Quản lý nước tưới hợp lý",
        ],
        severity_factors=["Độ ẩm cao", "Mưa nhiều", "Nhiệt độ 28-32°C"],
    ),

    "Bacterial Canker": DiseaseInfo(
        name="Bacterial Canker",
        plant="Mango",
        vietnamese_name="Bệnh loét vi khuẩn",
        description="Bệnh do bacterium Xanthomonas axonopodis pv. mangiferaeindicae. "
                    "Gây loét trên thân, cành, lá và quả.",
        treatments=[
            "Cắt bỏ cành bị bệnh, sát khuẩn dụng cụ",
            "Phun Bordeaux mixture sau khi cắt",
            "Bôi paste chứa铜 lên vết thương",
            "Tăng cường phân bón kali để cây khỏe",
        ],
        prevention=[
            "Tránh làm tổn thương vỏ cây",
            "Phun phòng ngừa vào đầu mùa mưa",
            "Quản lý côn trùng trung gian",
        ],
        severity_factors=["Mưa lớn", "Gió mạnh", "Cây yếu"],
    ),

    # === fungal diseases ===
    "Anthracnose": DiseaseInfo(
        name="Anthracnose",
        plant="Mango",
        vietnamese_name="Bệnh thán thư",
        description="Bệnh do nấm Colletotrichum gloeosporioides gây ra. "
                    "Tạo đốm đen trên quả, lá và cành. Đặc biệt nghiêm trọng trong mùa mưa.",
        treatments=[
            "Phun thuốc fungicide: Mancozeb, Carbendazim, hoặc Benomyl",
            "Cắt bỏ phần bị bệnh",
            "Bảo quản quả sau thu hoạch bằng热水处理 (52°C/5 phút)",
            "Phun phòng ngừa trước khi ra hoa",
        ],
        prevention=[
            "Phun phòng ngừa định kỳ 15-20 ngày/lần",
            "Quản lý nước tưới, tránh ẩm quá mức",
            "Loại bỏ rụi lá, quả rụng dưới gốc",
            "Bón phân cân đối NPK",
        ],
        severity_factors=["Mưa ẩm kéo dài", "Nhiệt độ 25-30°C", "Mật độ trồng dày"],
    ),

    "Powdery Mildew": DiseaseInfo(
        name="Powdery Mildew",
        plant="Cotton/Mango/Pumpkin",
        vietnamese_name="Bệnh phấn trắng",
        description="Bệnh do nấm Erysiphe cichoracearum. "
                    "Tạo lớp phấn trắng trên mặt trên của lá, làm lá cuộn lại và rụng.",
        treatments=[
            "Phun lưu huỳnh limestone (SMS) 0.3-0.5%",
            "Sử dụng fungicide: Tricyclazole, Hexaconazole",
            "Phun nước xà phòng loãng (1%)",
            "Tăng cường bón phân kali",
        ],
        prevention=[
            "Đảm bảo thông thoáng cho cây",
            "Tránh tưới nước lên lá",
            "Chọn giống kháng bệnh",
            "Phun phòng ngừa khi thời tiết khô hanh",
        ],
        severity_factors=["Thời tiết khô hanh", "Độ ẩm không khí cao", "Nhiệt độ 20-25°C"],
    ),

    "Black rot": DiseaseInfo(
        name="Black rot",
        plant="Apple/Grape/Cauliflower",
        vietnamese_name="Bệnh thối đen",
        description="Bệnh do nấm Alternaria brassicicola hoặc Guignardia bidwellii. "
                    "Gây thối đen trên lá, thân và quả.",
        treatments=[
            "Loại bỏ phần bị bệnh ngay",
            "Phun fungicide: Mancozeb, Chlorothalonil",
            "Cải thiện hệ thống thoát nước",
            "Bón phân kali để tăng cường sức đề kháng",
        ],
        prevention=[
            "Luân canh crops",
            "Quản lý độ ẩm đất",
            "Phun phòng ngừa vào đầu mùa",
            "Loại bỏ tàn dư thực vật",
        ],
        severity_factors=["Mưa ẩm", "Nhiệt độ thấp", "Đất thoát nước kém"],
    ),

    "BrownSpot": DiseaseInfo(
        name="BrownSpot",
        plant="Rice",
        vietnamese_name="Bệnh đốm nâu",
        description="Bệnh do nấm Bipolaris oryzae. "
                    "Tạo đốm nâu trên lá lúa, giảm năng suất.",
        treatments=[
            "Phun Carbendazim hoặc Isoprothiolane",
            "Sử dụngseed treatment trước khi gieo",
            "Bón phân kali và silic",
            "Quản lý nước hợp lý",
        ],
        prevention=[
            "Sử dụng giống kháng bệnh",
            "Bón phân cân đối, tránh thừa đạm",
            "Quản lý nước luân phiên khô-wet",
            "Làm đất kỹ sau vụ",
        ],
        severity_factors=["Đất nghèo dinh dưỡng", "Độ ẩm cao", "Nhiệt độ 25-30°C"],
    ),

    "Late blight": DiseaseInfo(
        name="Late blight",
        plant="Tomato/Potato",
        vietnamese_name="Bệnh hại muộn",
        description="Bệnh do nấm Phytophthora infestans. "
                    "Rất nguy hiểm, có thể phá hủy toàn bộ mùa màng trong vài ngày.",
        treatments=[
            "Phun Metalaxyl + Mancozeb (Ridomil Gold)",
            "Sử dụng Fosetyl-aluminum (Aliette)",
            "Cắt bỏ phần bị bệnh ngay",
            "Tăng cường bón phân kali",
        ],
        prevention=[
            "Chọn giống kháng bệnh",
            "Phun phòng ngừa khi thời tiết ẩm",
            "Quản lý nước tưới",
            "Luân canh crops",
        ],
        severity_factors=["Mưa ẩm kéo dài", "Nhiệt độ 15-25°C", "Sương mù"],
    ),

    "LeafBlast": DiseaseInfo(
        name="LeafBlast",
        plant="Rice",
        vietnamese_name="Bệnh cháy lá",
        description="Bệnh do nấm Magnaporthe oryzae. "
                    "Gây cháy lá dạng形 trên lá lúa, nghiêm trọng có thể gây chết cây.",
        treatments=[
            "Phun Tricyclazole hoặc Isoprothiolane",
            "Sử dụng seed treatment",
            "Bón phân kali và silic",
            "Quản lý nước hợp lý",
        ],
        prevention=[
            "Sử dụng giống kháng",
            "Bón phân cân đối",
            "Tránh bón đạm quá nhiều",
            "Quản lý nước luân phiên",
        ],
        severity_factors=["Độ ẩm cao", "Nhiệt độ 25-30°C", "Đất nghèo dinh dưỡng"],
    ),

    "Northern Leaf Blight": DiseaseInfo(
        name="Northern Leaf Blight",
        plant="Corn (maize)",
        vietnamese_name="Bệnh cháy lá phía bắc",
        description="Bệnh do nấm Exserohilum turcicum. "
                    "Tạo đốm dạng cigars trên lá ngô.",
        treatments=[
            "Phun Propiconazole hoặc Azoxystrobin",
            "Sử dụng giống lai kháng bệnh",
            "Bón phân kali",
            "Quản lý tàn dư mùa trước",
        ],
        prevention=[
            "Chọn giống lai F1 kháng",
            "Luân canh crops 2-3 năm",
            "Phun phòng ngừa khi thời tiết thuận lợi",
            "Làm sạch tàn dư cây trồng",
        ],
        severity_factors=["Mưa ẩm", "Nhiệt độ 18-27°C", "Sương dew kéo dài"],
    ),

    # === healthy ===
    "Healthy": DiseaseInfo(
        name="Healthy",
        plant="All",
        vietnamese_name="Khỏe mạnh",
        description="Cây không có dấu hiệu bệnh. Tiếp tục duy trì chế độ chăm sóc hiện tại.",
        treatments=[],
        prevention=[
            "Tiếp tục tưới nước, ánh sáng và dinh dưỡng hợp lý",
            "Kiểm tra định kỳ để phát hiện sớm dấu hiệu bệnh",
            "Duy trì vệ sinh vườn/ruộng",
        ],
        severity_factors=[],
    ),
}


# ---------------------------------------------------------------------------
# Severity Assessment
# ---------------------------------------------------------------------------

def assess_severity(
    confidence: float,
    leaf_count: int = 1,
    affected_leaves: int = 1,
    is_healthy: bool = False,
) -> Dict[str, Any]:
    """Đánh giá mức độ nghiêm trọng của bệnh.

    Args:
        confidence: Độ tin cậy của model (0-1)
        leaf_count: Tổng số lá được phân tích
        affected_leaves: Số lá bị bệnh
        is_healthy: Có phải cây khỏe mạnh không

    Returns:
        Dict chứa severity level, score, và message
    """
    if is_healthy:
        return {
            "level": "healthy",
            "score": 0,
            "message": "Cây khỏe mạnh, không phát hiện dấu hiệu bệnh.",
        }

    # Tính severity score dựa trên nhiều yếu tố
    score = 0

    # Factor 1: Confidence cao → bệnh rõ ràng hơn
    if confidence >= 0.85:
        score += 40
    elif confidence >= 0.65:
        score += 25
    elif confidence >= 0.45:
        score += 10
    else:
        score += 5  # Không chắc chắn

    # Factor 2: Tỷ lệ lá bị bệnh
    if leaf_count > 0:
        affected_ratio = affected_leaves / leaf_count
        if affected_ratio >= 0.8:
            score += 35  # Hầu hết lá bị bệnh
        elif affected_ratio >= 0.5:
            score += 25
        elif affected_ratio >= 0.2:
            score += 15
        else:
            score += 5

    # Factor 3: Số lượng lá bị ảnh hưởng
    if affected_leaves >= 5:
        score += 25
    elif affected_leaves >= 3:
        score += 15
    elif affected_leaves >= 1:
        score += 5

    # Determine level
    if score >= 70:
        level = "critical"
        message = "⚠️ Nghiêm trọng: Cây bị bệnh nặng, cần xử lý ngay lập tức!"
    elif score >= 50:
        level = "high"
        message = "🔴 Cao: Bệnh đang phát triển, cần xử lý sớm."
    elif score >= 30:
        level = "medium"
        message = "🟡 Trung bình: Phát hiện dấu hiệu bệnh, cần theo dõi."
    elif score >= 10:
        level = "low"
        message = "🟢 Thấp: Dấu hiệu bệnh nhẹ, tiếp tục theo dõi."
    else:
        level = "unknown"
        message = "❓ Không xác định: Cần chụp ảnh rõ hơn để đánh giá."

    return {
        "level": level,
        "score": score,
        "message": message,
    }


# ---------------------------------------------------------------------------
# Treatment Recommendations
# ---------------------------------------------------------------------------

def get_treatment_recommendations(
    disease_label: str,
    plant_name: Optional[str] = None,
    severity: Optional[Dict[str, Any]] = None,
) -> Dict[str, Any]:
    """Lấy khuyến nghị xử lý dựa trên nhãn bệnh.

    Args:
        disease_label: Nhãn bệnh từ model (vd: "Bacterial spot (Tomato)")
        plant_name: Tên cây (optional, sẽ parse từ label nếu không có)
        severity: Kết quả assess_severity (optional)

    Returns:
        Dict chứa description, treatments, prevention, và severity info
    """
    # Parse disease name from label
    disease_name, parsed_plant = _parse_disease_label(disease_label)
    plant = plant_name or parsed_plant

    # Find in knowledge base
    disease_info = _find_disease_info(disease_name)

    if disease_info:
        result = {
            "found_in_kb": True,
            "disease_name": disease_info.vietnamese_name,
            "plant_name": disease_info.plant if not plant else plant,
            "description": disease_info.description,
            "treatments": disease_info.treatments,
            "prevention": disease_info.prevention,
            "severity_factors": disease_info.severity_factors,
        }
    else:
        # Fallback for unknown diseases
        result = {
            "found_in_kb": False,
            "disease_name": disease_name,
            "plant_name": plant or "Không xác định",
            "description": f"Phát hiện dấu hiệu '{disease_label}' trên ảnh. "
                          "Khuyến nghị tham khảo chuyên gia nông nghiệp để xác nhận và có phác đồ xử lý phù hợp.",
            "treatments": [
                "Cách ly cây bị bệnh để tránh lây lan",
                "Loại bỏ lá/cành bệnh nặng",
                "Tham khảo chuyên gia để chọn thuốc đặc trị phù hợp",
            ],
            "prevention": [
                "Theo dõi cây thường xuyên",
                "Đảm bảo thông gió và ánh sáng hợp lý",
                "Vệ sinh dụng cụ làm vườn",
            ],
            "severity_factors": [],
        }

    # Add severity info if provided
    if severity:
        result["severity"] = severity
    else:
        result["severity"] = assess_severity(
            confidence=0.5,  # Default
            is_healthy=(disease_name.lower() == "healthy"),
        )

    return result


def _parse_disease_label(label: str) -> tuple[str, Optional[str]]:
    """Parse disease name và plant name từ label.

    VD: "Bacterial spot (Tomato)" → ("Bacterial spot", "Tomato")
        "Powdery Mildew (Cotton)" → ("Powdery Mildew", "Cotton")
        "Healthy" → ("Healthy", None)
    """
    import re
    match = re.match(r'^(.+?)\s*\((.+?)\)\s*$', label)
    if match:
        return match.group(1).strip(), match.group(2).strip()
    return label.strip(), None


def _find_disease_info(disease_name: str) -> Optional[DiseaseInfo]:
    """Tìm thông tin bệnh trong knowledge base."""
    # Try exact match
    for key, info in DISEASE_DATABASE.items():
        if key.lower() == disease_name.lower():
            return info

    # Try partial match
    disease_lower = disease_name.lower()
    for key, info in DISEASE_DATABASE.items():
        if key.lower() in disease_lower or disease_lower in key.lower():
            return info

    return None


# ---------------------------------------------------------------------------
# Export for Laravel
# ---------------------------------------------------------------------------

def get_knowledge_base_summary() -> Dict[str, Any]:
    """Tóm tắt knowledge base để export cho Laravel service."""
    return {
        "total_diseases": len(DISEASE_DATABASE),
        "diseases": [
            {
                "name": info.name,
                "plant": info.plant,
                "vietnamese_name": info.vietnamese_name,
            }
            for info in DISEASE_DATABASE.values()
        ],
    }
