import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ofan_app/core/repositories/auth_repository_api.dart';
import '../services/api_service.dart';
import '../repositories/product_repository_api.dart';
import '../repositories/customer_repository_api.dart';
import '../repositories/order_repository_api.dart';

// API Service Provider
final apiServiceProvider = Provider<ApiService>((ref) {
  return ApiService();
});

final productRepositoryApiProvider = Provider<ProductRepositoryApi>((ref) {
  final apiService = ref.watch(apiServiceProvider);
  return ProductRepositoryApi(apiService);
});

final authRepositoryEnhancedProvider = Provider<AuthRepositoryEnhanced>((ref) {
  final apiService = ref.watch(apiServiceProvider);
  return AuthRepositoryEnhanced(apiService);
});

final customerRepositoryApiProvider = Provider<CustomerRepositoryApi>((ref) {
  final apiService = ref.watch(apiServiceProvider);
  return CustomerRepositoryApi(apiService);
});

final orderRepositoryApiProvider = Provider<OrderRepositoryApi>((ref) {
  final apiService = ref.watch(apiServiceProvider);
  return OrderRepositoryApi(apiService);
});
