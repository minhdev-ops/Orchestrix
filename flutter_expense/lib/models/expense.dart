import 'expense_category.dart';

class Expense {
  final int id;
  final String uuid;
  final String title;
  final double amount;
  final String? expenseDate;
  final String? paymentMethod;
  final String? receiptImageUrl;
  final String? note;
  final String currency;
  final ExpenseCategory? category;
  final String? createdAt;

  const Expense({
    required this.id,
    required this.uuid,
    required this.title,
    required this.amount,
    this.expenseDate,
    this.paymentMethod,
    this.receiptImageUrl,
    this.note,
    this.currency = 'VND',
    this.category,
    this.createdAt,
  });

  factory Expense.fromJson(Map<String, dynamic> json) {
    return Expense(
      id: json['id'] as int,
      uuid: json['uuid'] as String,
      title: json['title'] as String,
      amount: (json['amount'] as num).toDouble(),
      expenseDate: json['expense_date'] as String?,
      paymentMethod: json['payment_method'] as String?,
      receiptImageUrl: json['receipt_image_url'] as String?,
      note: json['note'] as String?,
      currency: json['currency'] as String? ?? 'VND',
      category: json['category'] == null
          ? null
          : ExpenseCategory.fromJson(json['category'] as Map<String, dynamic>),
      createdAt: json['created_at'] as String?,
    );
  }
}