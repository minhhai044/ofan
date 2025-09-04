import '../models/product.dart';

class ProductRepository {
  // Mock data for products
  static final List<Product> _products = [
    const Product(
      id: '1',
      name: 'Coffee Latte',
      description: 'Premium coffee latte with steamed milk',
      price: 4.50,
      stock: 25,
      category: 'Beverages',
      barcode: '1234567890123',
      imageUrl: 'https://via.placeholder.com/150x150/8B4513/FFFFFF?text=Coffee',
    ),
    const Product(
      id: '2',
      name: 'Chocolate Cake',
      description: 'Rich chocolate cake with cream frosting',
      price: 12.99,
      stock: 8,
      category: 'Desserts',
      barcode: '1234567890124',
      imageUrl: 'https://via.placeholder.com/150x150/8B4513/FFFFFF?text=Cake',
    ),
    const Product(
      id: '3',
      name: 'Caesar Salad',
      description: 'Fresh romaine lettuce with caesar dressing',
      price: 8.75,
      stock: 15,
      category: 'Salads',
      barcode: '1234567890125',
      imageUrl: 'https://via.placeholder.com/150x150/228B22/FFFFFF?text=Salad',
    ),
    const Product(
      id: '4',
      name: 'Burger Deluxe',
      description: 'Beef burger with cheese, lettuce, and tomato',
      price: 15.50,
      stock: 12,
      category: 'Main Course',
      barcode: '1234567890126',
      imageUrl: 'https://via.placeholder.com/150x150/FF6347/FFFFFF?text=Burger',
    ),
    const Product(
      id: '5',
      name: 'Orange Juice',
      description: 'Fresh squeezed orange juice',
      price: 3.25,
      stock: 30,
      category: 'Beverages',
      barcode: '1234567890127',
      imageUrl: 'https://via.placeholder.com/150x150/FFA500/FFFFFF?text=Juice',
    ),
    const Product(
      id: '6',
      name: 'Pizza Margherita',
      description: 'Classic pizza with tomato, mozzarella, and basil',
      price: 18.00,
      stock: 6,
      category: 'Main Course',
      barcode: '1234567890128',
      imageUrl: 'https://via.placeholder.com/150x150/FF4500/FFFFFF?text=Pizza',
    ),
    const Product(
      id: '7',
      name: 'Ice Cream Vanilla',
      description: 'Premium vanilla ice cream',
      price: 5.99,
      stock: 20,
      category: 'Desserts',
      barcode: '1234567890129',
      imageUrl: 'https://via.placeholder.com/150x150/F5F5DC/000000?text=Ice+Cream',
    ),
    const Product(
      id: '8',
      name: 'Green Tea',
      description: 'Organic green tea',
      price: 2.75,
      stock: 40,
      category: 'Beverages',
      barcode: '1234567890130',
      imageUrl: 'https://via.placeholder.com/150x150/9ACD32/FFFFFF?text=Tea',
    ),
  ];

  Future<List<Product>> getAllProducts() async {
    // Simulate API delay
    await Future.delayed(const Duration(milliseconds: 500));
    return List.from(_products);
  }

  Future<List<Product>> getProductsByCategory(String category) async {
    await Future.delayed(const Duration(milliseconds: 300));
    return _products.where((product) => product.category == category).toList();
  }

  Future<Product?> getProductById(String id) async {
    await Future.delayed(const Duration(milliseconds: 200));
    try {
      return _products.firstWhere((product) => product.id == id);
    } catch (e) {
      return null;
    }
  }

  Future<Product?> getProductByBarcode(String barcode) async {
    await Future.delayed(const Duration(milliseconds: 200));
    try {
      return _products.firstWhere((product) => product.barcode == barcode);
    } catch (e) {
      return null;
    }
  }

  Future<List<Product>> searchProducts(String query) async {
    await Future.delayed(const Duration(milliseconds: 300));
    final lowercaseQuery = query.toLowerCase();
    return _products
        .where((product) =>
            product.name.toLowerCase().contains(lowercaseQuery) ||
            product.description.toLowerCase().contains(lowercaseQuery) ||
            product.category.toLowerCase().contains(lowercaseQuery))
        .toList();
  }

  Future<List<String>> getCategories() async {
    await Future.delayed(const Duration(milliseconds: 200));
    return _products.map((product) => product.category).toSet().toList();
  }

  Future<Product> updateProduct(Product product) async {
    await Future.delayed(const Duration(milliseconds: 400));
    final index = _products.indexWhere((p) => p.id == product.id);
    if (index != -1) {
      _products[index] = product;
      return product;
    }
    throw Exception('Product not found');
  }

  Future<void> deleteProduct(String id) async {
    await Future.delayed(const Duration(milliseconds: 300));
    _products.removeWhere((product) => product.id == id);
  }

  Future<Product> addProduct(Product product) async {
    await Future.delayed(const Duration(milliseconds: 400));
    _products.add(product);
    return product;
  }
}
