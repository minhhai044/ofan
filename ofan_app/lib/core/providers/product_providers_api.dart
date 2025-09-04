import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ofan_app/core/repositories/product_repository_api.dart';
import '../models/product.dart';
import 'api_providers.dart';

// Products list provider với API
final productsApiProvider = FutureProvider<List<Product>>((ref) async {
  final repository = ref.read(productRepositoryApiProvider);
  return repository.getAllProducts();
});

// Product categories provider với API
final categoriesApiProvider = FutureProvider<List<String>>((ref) async {
  final repository = ref.read(productRepositoryApiProvider);
  return repository.getCategories();
});

// Search query provider
final productSearchQueryApiProvider = StateProvider<String>((ref) => '');

// Selected category filter provider
final selectedCategoryApiProvider = StateProvider<String?>((ref) => null);

// Filtered products provider với API
final filteredProductsApiProvider = FutureProvider<List<Product>>((ref) async {
  final repository = ref.read(productRepositoryApiProvider);
  final searchQuery = ref.watch(productSearchQueryApiProvider);
  final selectedCategory = ref.watch(selectedCategoryApiProvider);
  
  List<Product> products;
  
  if (searchQuery.isNotEmpty) {
    products = await repository.searchProducts(searchQuery);
  } else if (selectedCategory != null && selectedCategory.isNotEmpty) {
    products = await repository.getProductsByCategory(selectedCategory);
  } else {
    products = await repository.getAllProducts();
  }
  
  return products;
});

// Product by ID provider với API
final productByIdApiProvider = FutureProvider.family<Product?, String>((ref, id) async {
  final repository = ref.read(productRepositoryApiProvider);
  return repository.getProductById(id);
});

// Product by barcode provider với API
final productByBarcodeApiProvider = FutureProvider.family<Product?, String>((ref, barcode) async {
  final repository = ref.read(productRepositoryApiProvider);
  return repository.getProductByBarcode(barcode);
});

// StateNotifier cho product management
class ProductsNotifier extends StateNotifier<AsyncValue<List<Product>>> {
  final ProductRepositoryApi _repository;
  
  ProductsNotifier(this._repository) : super(const AsyncValue.loading()) {
    loadProducts();
  }
  
  Future<void> loadProducts() async {
    state = const AsyncValue.loading();
    try {
      final products = await _repository.getAllProducts();
      state = AsyncValue.data(products);
    } catch (error, stackTrace) {
      state = AsyncValue.error(error, stackTrace);
    }
  }
  
  Future<void> addProduct(Product product) async {
    try {
      await _repository.addProduct(product);
      await loadProducts(); // Reload list
    } catch (error) {
      rethrow;
    }
  }
  
  Future<void> updateProduct(Product product) async {
    try {
      await _repository.updateProduct(product);
      await loadProducts(); // Reload list
    } catch (error) {
      rethrow;
    }
  }
  
  Future<void> deleteProduct(String id) async {
    try {
      await _repository.deleteProduct(id);
      await loadProducts(); // Reload list
    } catch (error) {
      rethrow;
    }
  }
}

final productsNotifierApiProvider = StateNotifierProvider<ProductsNotifier, AsyncValue<List<Product>>>((ref) {
  final repository = ref.watch(productRepositoryApiProvider);
  return ProductsNotifier(repository);
});
