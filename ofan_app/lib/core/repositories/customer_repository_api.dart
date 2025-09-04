import '../models/customer.dart';
import '../services/api_service.dart';
import '../config/api_config.dart';

class CustomerRepositoryApi {
  final ApiService _apiService;

  CustomerRepositoryApi(this._apiService);

  Future<List<Customer>> getAllCustomers({
    int page = 1,
    int limit = 20,
  }) async {
    try {
      final response = await _apiService.get(
        ApiConfig.customers,
        queryParameters: {
          'page': page,
          'limit': limit,
        },
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data['items'] as List)
            .map((item) => Customer.fromJson(item as Map<String, dynamic>))
            .toList(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<List<Customer>> searchCustomers(
    String query, {
    int page = 1,
    int limit = 20,
  }) async {
    try {
      final response = await _apiService.get(
        ApiConfig.customerSearch,
        queryParameters: {
          'q': query,
          'page': page,
          'limit': limit,
        },
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data['items'] as List)
            .map((item) => Customer.fromJson(item as Map<String, dynamic>))
            .toList(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<Customer?> getCustomerById(String id) async {
    try {
      final response = await _apiService.get('${ApiConfig.customers}/$id');

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => Customer.fromJson(data as Map<String, dynamic>),
      );

      return apiResponse.data;
    } on ApiException catch (e) {
      if (e.code == 'HTTP_404') {
        return null;
      }
      throw Exception(e.message);
    }
  }

  Future<Customer> addCustomer(Customer customer) async {
    try {
      final response = await _apiService.post(
        ApiConfig.customers,
        data: customer.toJson(),
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => Customer.fromJson(data as Map<String, dynamic>),
      );

      if (apiResponse.success && apiResponse.data != null) {
        return apiResponse.data!;
      } else {
        throw Exception(apiResponse.error ?? 'Thêm khách hàng thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<Customer> updateCustomer(Customer customer) async {
    try {
      final response = await _apiService.put(
        '${ApiConfig.customers}/${customer.id}',
        data: customer.toJson(),
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => Customer.fromJson(data as Map<String, dynamic>),
      );

      if (apiResponse.success && apiResponse.data != null) {
        return apiResponse.data!;
      } else {
        throw Exception(apiResponse.error ?? 'Cập nhật khách hàng thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<void> deleteCustomer(String id) async {
    try {
      final response = await _apiService.delete('${ApiConfig.customers}/$id');
      
      if (response.statusCode != 204 && response.statusCode != 200) {
        throw Exception('Xóa khách hàng thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<List<Customer>> getTopCustomers({int limit = 10}) async {
    try {
      final response = await _apiService.get(
        '${ApiConfig.customers}/top',
        queryParameters: {'limit': limit},
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data['items'] as List)
            .map((item) => Customer.fromJson(item as Map<String, dynamic>))
            .toList(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }
}
