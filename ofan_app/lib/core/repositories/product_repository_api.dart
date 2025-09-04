import '../models/product.dart';
import '../services/api_service.dart';
import '../config/api_config.dart';

class ProductRepositoryApi {
  final ApiService _apiService;

  ProductRepositoryApi(this._apiService);

  Future<List<Product>> getAllProducts({
    int page = 1,
    int limit = 20,
    String? category,
  }) async {
    try {
      final queryParams = <String, dynamic>{
        'page': page,
        'limit': limit,
      };
      
      if (category != null) {
        queryParams['category'] = category;
      }

      final response = await _apiService.get(
        ApiConfig.products,
        queryParameters: queryParams,
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data['items'] as List)
            .map((item) => Product.fromJson(item as Map<String, dynamic>))
            .toList(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<List<Product>> getProductsByCategory(String category) async {
    return getAllProducts(category: category);
  }

  Future<Product?> getProductById(String id) async {
    try {
      final response = await _apiService.get('${ApiConfig.products}/$id');

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => Product.fromJson(data as Map<String, dynamic>),
      );

      return apiResponse.data;
    } on ApiException catch (e) {
      if (e.code == 'HTTP_404') {
        return null;
      }
      throw Exception(e.message);
    }
  }

  Future<Product?> getProductByBarcode(String barcode) async {
    try {
      final response = await _apiService.get(
        ApiConfig.products,
        queryParameters: {'barcode': barcode},
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) {
          final items = data['items'] as List;
          return items.isNotEmpty 
              ? Product.fromJson(items.first as Map<String, dynamic>)
              : null;
        },
      );

      return apiResponse.data;
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<List<Product>> searchProducts(
    String query, {
    int page = 1,
    int limit = 20,
  }) async {
    try {
      final response = await _apiService.get(
        ApiConfig.productSearch,
        queryParameters: {
          'q': query,
          'page': page,
          'limit': limit,
        },
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data['items'] as List)
            .map((item) => Product.fromJson(item as Map<String, dynamic>))
            .toList(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<List<String>> getCategories() async {
    try {
      final response = await _apiService.get(ApiConfig.productCategories);

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => (data as List).cast<String>(),
      );

      return apiResponse.data ?? [];
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<Product> updateProduct(Product product) async {
    try {
      final response = await _apiService.put(
        '${ApiConfig.products}/${product.id}',
        data: product.toJson(),
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => Product.fromJson(data as Map<String, dynamic>),
      );

      if (apiResponse.success && apiResponse.data != null) {
        return apiResponse.data!;
      } else {
        throw Exception(apiResponse.error ?? 'Cập nhật sản phẩm thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<void> deleteProduct(String id) async {
    try {
      final response = await _apiService.delete('${ApiConfig.products}/$id');
      
      if (response.statusCode != 204 && response.statusCode != 200) {
        throw Exception('Xóa sản phẩm thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<Product> addProduct(Product product) async {
    try {
      final response = await _apiService.post(
        ApiConfig.products,
        data: product.toJson(),
      );

      final apiResponse = ApiResponse.fromJson(
        response.data,
        (data) => Product.fromJson(data as Map<String, dynamic>),
      );

      if (apiResponse.success && apiResponse.data != null) {
        return apiResponse.data!;
      } else {
        throw Exception(apiResponse.error ?? 'Thêm sản phẩm thất bại');
      }
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }

  Future<void> updateStock(String productId, int newStock) async {
    try {
      await _apiService.put(
        '${ApiConfig.products}/$productId/stock',
        data: {'stock': newStock},
      );
    } on ApiException catch (e) {
      throw Exception(e.message);
    }
  }
}
