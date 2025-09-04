import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/order.dart';
import '../repositories/order_repository.dart';

// Repository provider
final orderRepositoryProvider = Provider<OrderRepository>((ref) {
  return OrderRepository();
});

// Orders list provider
final ordersProvider = FutureProvider<List<Order>>((ref) async {
  final repository = ref.read(orderRepositoryProvider);
  return repository.getAllOrders();
});

// Orders by customer provider
final ordersByCustomerProvider = FutureProvider.family<List<Order>, String>((ref, customerId) async {
  final repository = ref.read(orderRepositoryProvider);
  return repository.getOrdersByCustomer(customerId);
});

// Orders by status provider
final ordersByStatusProvider = FutureProvider.family<List<Order>, OrderStatus>((ref, status) async {
  final repository = ref.read(orderRepositoryProvider);
  return repository.getOrdersByStatus(status);
});

// Total revenue provider
final totalRevenueProvider = FutureProvider<double>((ref) async {
  final repository = ref.read(orderRepositoryProvider);
  return repository.getTotalRevenue();
});

// Total orders count provider
final totalOrdersCountProvider = FutureProvider<int>((ref) async {
  final repository = ref.read(orderRepositoryProvider);
  return repository.getTotalOrdersCount();
});

// Best selling products provider
final bestSellingProductsProvider = FutureProvider<Map<String, int>>((ref) async {
  final repository = ref.read(orderRepositoryProvider);
  return repository.getBestSellingProducts(limit: 5);
});

// Daily sales provider
final dailySalesProvider = FutureProvider<List<Map<String, dynamic>>>((ref) async {
  final repository = ref.read(orderRepositoryProvider);
  return repository.getDailySales(days: 7);
});

// Today's revenue provider
final todayRevenueProvider = FutureProvider<double>((ref) async {
  final repository = ref.read(orderRepositoryProvider);
  final now = DateTime.now();
  final startOfDay = DateTime(now.year, now.month, now.day);
  final endOfDay = startOfDay.add(const Duration(days: 1));
  return repository.getTotalRevenue(start: startOfDay, end: endOfDay);
});

// Today's orders count provider
final todayOrdersCountProvider = FutureProvider<int>((ref) async {
  final repository = ref.read(orderRepositoryProvider);
  final now = DateTime.now();
  final startOfDay = DateTime(now.year, now.month, now.day);
  final endOfDay = startOfDay.add(const Duration(days: 1));
  return repository.getTotalOrdersCount(start: startOfDay, end: endOfDay);
});
