import '../models/order.dart';
import '../services/api_service.dart';
import '../config/api_config.dart';

class OrderRepositoryApi {
  final ApiService _apiService;

  OrderRepositoryApi(this._apiService);

  Future<List<Order>> getAllOrders({
    int page = 1,
    int limit = 20,
  }) async {
    try {
      final response = await _apiService.get(
        ApiConfig.orders,
        queryParameters: {
          'page': page,
          'limit': limit,
        },
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data['items'] as List)
            .map((item) => Order.fromJson(item as Map<String, dynamic>))
            .toList(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<Order?> getOrderById(String id) async {
    try {
      final response = await _apiService.get('${ApiConfig.orders}/$id');

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => Order.fromJson(data as Map<String, dynamic>),
      );

      return apiResponse.data;
    } on ApiException catch (e) {
      if (e.code == 'HTTP_404') {
        return null;
      }
      throw Exception(e.message);
    }
  }

  Future<List<Order>> getOrdersByCustomer(String customerId) async {
    try {
      final response = await _apiService.get(
        ApiConfig.ordersByCustomer,
        queryParameters: {'customer_id': customerId},
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data['items'] as List)
            .map((item) => Order.fromJson(item as Map<String, dynamic>))
            .toList(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<List<Order>> getOrdersByStatus(OrderStatus status) async {
    try {
      final response = await _apiService.get(
        ApiConfig.orders,
        queryParameters: {'status': status.toString().split('.').last},
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data['items'] as List)
            .map((item) => Order.fromJson(item as Map<String, dynamic>))
            .toList(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<List<Order>> getOrdersByDateRange(DateTime start, DateTime end) async {
    try {
      final response = await _apiService.get(
        ApiConfig.orders,
        queryParameters: {
          'start_date': start.toIso8601String(),
          'end_date': end.toIso8601String(),
        },
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data['items'] as List)
            .map((item) => Order.fromJson(item as Map<String, dynamic>))
            .toList(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<Order> createOrder(Order order) async {
    try {
      final response = await _apiService.post(
        ApiConfig.orders,
        data: order.toJson(),
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => Order.fromJson(data as Map<String, dynamic>),
      );

      if (apiResponse.success && apiResponse.data != null) {
        return apiResponse.data!;
      } else {
        throw Exception(apiResponse.error ?? 'Tạo đơn hàng thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<Order> updateOrder(Order order) async {
    try {
      final response = await _apiService.put(
        '${ApiConfig.orders}/${order.id}',
        data: order.toJson(),
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => Order.fromJson(data as Map<String, dynamic>),
      );

      if (apiResponse.success && apiResponse.data != null) {
        return apiResponse.data!;
      } else {
        throw Exception(apiResponse.error ?? 'Cập nhật đơn hàng thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<void> deleteOrder(String id) async {
    try {
      final response = await _apiService.delete('${ApiConfig.orders}/$id');
      
      if (response.statusCode != 204 && response.statusCode != 200) {
        throw Exception('Xóa đơn hàng thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  // Analytics methods
  Future<double> getTotalRevenue({DateTime? start, DateTime? end}) async {
    try {
      final queryParams = <String, dynamic>{};
      if (start != null) queryParams['start_date'] = start.toIso8601String();
      if (end != null) queryParams['end_date'] = end.toIso8601String();

      final response = await _apiService.get(
        '${ApiConfig.ordersAnalytics}/revenue',
        queryParameters: queryParams,
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);
      return (apiResponse.data?['total_revenue'] ?? 0.0).toDouble();
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<int> getTotalOrdersCount({DateTime? start, DateTime? end}) async {
    try {
      final queryParams = <String, dynamic>{};
      if (start != null) queryParams['start_date'] = start.toIso8601String();
      if (end != null) queryParams['end_date'] = end.toIso8601String();

      final response = await _apiService.get(
        '${ApiConfig.ordersAnalytics}/count',
        queryParameters: queryParams,
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);
      return apiResponse.data?['total_orders'] ?? 0;
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<Map<String, int>> getBestSellingProducts({int limit = 10}) async {
    try {
      final response = await _apiService.get(
        '${ApiConfig.ordersAnalytics}/best-sellers',
        queryParameters: {'limit': limit},
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);
      final items = apiResponse.data?['items'] as List? ?? [];
      
      return Map<String, int>.fromEntries(
        items.map((item) => MapEntry<String, int>(
          item['product_name'] as String,
          item['quantity'] as int,
        )),
      );
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<List<Map<String, dynamic>>> getDailySales({int days = 30}) async {
    try {
      final response = await _apiService.get(
        '${ApiConfig.ordersAnalytics}/daily-sales',
        queryParameters: {'days': days},
      );

      final apiResponse = ApiResponse.fromJson(response.data, (data) => data);
      final items = apiResponse.data?['items'] as List? ?? [];
      
      return items.cast<Map<String, dynamic>>();
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }
}
