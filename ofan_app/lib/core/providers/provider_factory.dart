import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../config/app_config.dart';
import '../models/product.dart';
import '../models/customer.dart';
import '../models/order.dart';

// Mock providers
import 'product_providers.dart' as mock;
import 'customer_providers.dart' as mock;
import 'order_providers.dart' as mock;

// API providers
import 'product_providers_api.dart' as api;
import 'api_providers.dart';

/// Factory để switch giữa mock và API providers dựa trên config
class ProviderFactory {
  // Product providers
  static Provider<FutureProvider<List<Product>>> get productsProvider {
    return Provider((ref) {
      return AppConfig.useApiRepositories 
          ? api.productsApiProvider 
          : mock.productsProvider;
    });
  }
  
  static Provider<FutureProvider<List<String>>> get categoriesProvider {
    return Provider((ref) {
      return AppConfig.useApiRepositories 
          ? api.categoriesApiProvider 
          : mock.categoriesProvider;
    });
  }
  
  static Provider<FutureProvider<List<Product>>> get filteredProductsProvider {
    return Provider((ref) {
      return AppConfig.useApiRepositories 
          ? api.filteredProductsApiProvider 
          : mock.filteredProductsProvider;
    });
  }
  
  static Provider<StateProvider<String>> get productSearchQueryProvider {
    return Provider((ref) {
      return AppConfig.useApiRepositories 
          ? api.productSearchQueryApiProvider 
          : mock.productSearchQueryProvider;
    });
  }
  
  static Provider<StateProvider<String?>> get selectedCategoryProvider {
    return Provider((ref) {
      return AppConfig.useApiRepositories 
          ? api.selectedCategoryApiProvider 
          : mock.selectedCategoryProvider;
    });
  }
  
  // Customer providers
  static Provider<FutureProvider<List<Customer>>> get customersProvider {
    return Provider((ref) {
      return AppConfig.useApiRepositories 
          ? _createCustomerApiProvider(ref)
          : mock.customersProvider;
    });
  }
  
  // Order providers  
  static Provider<FutureProvider<List<Order>>> get ordersProvider {
    return Provider((ref) {
      return AppConfig.useApiRepositories 
          ? _createOrderApiProvider(ref)
          : mock.ordersProvider;
    });
  }
  
  // Helper methods để tạo API providers
  static FutureProvider<List<Customer>> _createCustomerApiProvider(Ref ref) {
    return FutureProvider<List<Customer>>((ref) async {
      final repository = ref.read(customerRepositoryApiProvider);
      return repository.getAllCustomers();
    });
  }
  
  static FutureProvider<List<Order>> _createOrderApiProvider(Ref ref) {
    return FutureProvider<List<Order>>((ref) async {
      final repository = ref.read(orderRepositoryApiProvider);
      return repository.getAllOrders();
    });
  }
}

// Convenience providers để sử dụng trong UI
final currentProductsProvider = Provider<FutureProvider<List<Product>>>((ref) {
  return ref.watch(ProviderFactory.productsProvider);
});

final currentCategoriesProvider = Provider<FutureProvider<List<String>>>((ref) {
  return ref.watch(ProviderFactory.categoriesProvider);
});

final currentFilteredProductsProvider = Provider<FutureProvider<List<Product>>>((ref) {
  return ref.watch(ProviderFactory.filteredProductsProvider);
});
