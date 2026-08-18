import 'dart:io';

import 'package:dio/dio.dart';
import 'package:intl/intl.dart';

import '../models/expense.dart';
import '../models/expense_category.dart';
import 'api_client.dart';

class ExpenseApi {
  ExpenseApi(this._client);

  final ApiClient _client;

  Future<List<ExpenseCategory>> fetchCategories() async {
    final response = await _client.dio.get('/expense-categories');
    final data = response.data['data'] as List<dynamic>;
    return data
        .map((e) => ExpenseCategory.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<ExpenseCategory> createCategory({
    required String name,
    String? icon,
    String? color,
  }) async {
    final response = await _client.dio.post(
      '/expense-categories/create',
      data: {
        'name': name,
        if (icon != null) 'icon': icon,
        if (color != null) 'color': color,
      },
    );
    return ExpenseCategory.fromJson(response.data['data'] as Map<String, dynamic>);
  }

  Future<Expense> createExpense({
    required String title,
    required double amount,
    required int categoryId,
    DateTime? expenseDate,
    String? paymentMethod,
    String? note,
    File? receiptImage,
  }) async {
    final form = FormData();

    form.fields.addAll({
      'title': title,
      'amount': amount.toStringAsFixed(2),
      'expense_category_id': '$categoryId',
      if (expenseDate != null)
        'expense_date': DateFormat('yyyy-MM-dd').format(expenseDate),
      if (paymentMethod != null) 'payment_method': paymentMethod,
      if (note != null && note.isNotEmpty) 'note': note,
    });

    if (receiptImage != null) {
      final name = receiptImage.path.split('/').last;
      form.files.add(MapEntry(
        'receipt_image',
        await MultipartFile.fromFile(receiptImage.path, filename: name),
      ));
    }

    final response = await _client.dio.post('/expenses/create', data: form);
    return Expense.fromJson(response.data['data'] as Map<String, dynamic>);
  }
}