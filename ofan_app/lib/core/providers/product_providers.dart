import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/product.dart';
import '../repositories/product_repository.dart';

// Repository provider
final productRepositoryProvider = Provider<ProductRepository>((ref) {
  return ProductRepository();
});

// Products list provider
final productsProvider = FutureProvider<List<Product>>((ref) async {
  final repository = ref.read(productRepositoryProvider);
  return repository.getAllProducts();
});

// Product categories provider
final categoriesProvider = FutureProvider<List<String>>((ref) async {
  final repository = ref.read(productRepositoryProvider);
  return repository.getCategories();
});

// Search query provider
final productSearchQueryProvider = StateProvider<String>((ref) => '');

// Selected category filter provider
final selectedCategoryProvider = StateProvider<String?>((ref) => null);

// Filtered products provider
final filteredProductsProvider = FutureProvider<List<Product>>((ref) async {
  final repository = ref.read(productRepositoryProvider);
  final searchQuery = ref.watch(productSearchQueryProvider);
  final selectedCategory = ref.watch(selectedCategoryProvider);
  
  List<Product> products;
  
  if (searchQuery.isNotEmpty) {
    products = await repository.searchProducts(searchQuery);
  } else {
    products = await repository.getAllProducts();
  }
  
  if (selectedCategory != null && selectedCategory.isNotEmpty) {
    products = products.where((product) => product.category == selectedCategory).toList();
  }
  
  return products;
});

// Product by ID provider
final productByIdProvider = FutureProvider.family<Product?, String>((ref, id) async {
  final repository = ref.read(productRepositoryProvider);
  return repository.getProductById(id);
});

// Product by barcode provider
final productByBarcodeProvider = FutureProvider.family<Product?, String>((ref, barcode) async {
  final repository = ref.read(productRepositoryProvider);
  return repository.getProductByBarcode(barcode);
});