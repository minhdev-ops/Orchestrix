class ExpenseCategory {
  final int id;
  final String uuid;
  final String name;
  final String? icon;
  final String? color;

  const ExpenseCategory({
    required this.id,
    required this.uuid,
    required this.name,
    this.icon,
    this.color,
  });

  factory ExpenseCategory.fromJson(Map<String, dynamic> json) {
    return ExpenseCategory(
      id: json['id'] as int,
      uuid: json['uuid'] as String? ?? '',
      name: json['name'] as String,
      icon: json['icon'] as String?,
      color: json['color'] as String?,
    );
  }

  Map<String, dynamic> toCreateJson() {
    return {
      'name': name,
      if (icon != null) 'icon': icon,
      if (color != null) 'color': color,
    };
  }
}