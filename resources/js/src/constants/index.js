export const PROJECTS = [
    {
        id: 1,
        title: "Quantum Ledger v2",
        category: "Hệ thống phân tán",
        image: "https://images.unsplash.com/photo-1639762681485-074b7f938ba0?auto=format&fit=crop&q=80&w=1200",
        description: "Công cụ lập chỉ mục giao dịch thông lượng cao cho môi trường đa chuỗi, xử lý hơn 50.000 sự kiện mỗi giây.",
        tech: ["Rust", "gRPC", "PostgreSQL"],
        codeLink: "https://github.com/orchestrix/ledger",
        demoLink: "https://ledger-demo.orchestrix.io"
    },
    {
        id: 2,
        title: "Orbit Mesh",
        category: "Hạ tầng",
        image: "https://images.unsplash.com/photo-1558494949-ef010cbdcc51?auto=format&fit=crop&q=80&w=1200",
        description: "Mạng lưới dịch vụ tự phục hồi (self-healing) cho các cụm Kubernetes phân tán toàn cầu với bảo mật zero-trust.",
        tech: ["Go", "Kubernetes", "Envoy"],
        codeLink: "https://github.com/orchestrix/orbit",
        demoLink: "https://orbit-stats.orchestrix.io"
    },
    {
        id: 3,
        title: "Deep Signal",
        category: "Kỹ thuật dữ liệu",
        image: "https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&q=80&w=1200",
        description: "Hệ thống phân tích tâm lý thị trường thời gian thực sử dụng pipeline Machine Learning và lưu trữ độ trễ thấp.",
        tech: ["Python", "PyTorch", "Kafka"],
        codeLink: "https://github.com/orchestrix/signal",
        demoLink: "https://signal-dash.orchestrix.io"
    }
];

export const SKILLS = [
    {
        name: "Cloud Native",
        icon: "cloud",
        brandColor: "#58a6ff",
        size: "large",
        desc: "Kiến trúc hệ thống quy mô lớn. Kubernetes, AWS và quy trình DevOps hiện đại."
    },
    {
        name: "Hệ thống phân tán",
        icon: "hub",
        brandColor: "#39c5bb",
        size: "medium",
        desc: "Event sourcing, Rust/Go hiệu năng cao và các mô hình gRPC."
    },
    {
        name: "Kỹ thuật Laravel",
        icon: "api",
        brandColor: "#ff2d20",
        size: "medium",
        desc: "Kiến trúc Backend cao cấp với bảo mật và hiệu suất tối ưu."
    },
    {
        name: "Vận hành Docker",
        icon: "dock",
        brandColor: "#2496ed",
        size: "small",
        desc: "Container hóa và xây dựng pipeline CI/CD."
    },
    {
        name: "Chất lượng SonarQube",
        icon: "analytics",
        brandColor: "#4e9bcd",
        size: "small",
        desc: "Phân tích mã nguồn tĩnh và giám sát sức khỏe code."
    }
];
