import '../models/order.dart';
import '../models/cart_item.dart';
import '../models/customer.dart';
import '../models/product.dart';

class OrderRepository {
  // Mock data for orders
  static final List<Order> _orders = [
    Order(
      id: '1',
      items: [
        CartItem(
          product: const Product(
            id: '1',
            name: 'Coffee Latte',
            description: 'Premium coffee latte with steamed milk',
            price: 4.50,
            stock: 25,
            category: 'Beverages',
            barcode: '1234567890123',
          ),
          quantity: 2,
          unitPrice: 4.50,
        ),
        CartItem(
          product: const Product(
            id: '2',
            name: 'Chocolate Cake',
            description: 'Rich chocolate cake with cream frosting',
            price: 12.99,
            stock: 8,
            category: 'Desserts',
            barcode: '1234567890124',
          ),
          quantity: 1,
          unitPrice: 12.99,
        ),
      ],
      customer: Customer(
        id: '1',
        name: 'John Smith',
        email: 'john.smith@email.com',
        phone: '+1-555-0101',
        address: '123 Main St, City, State 12345',
        createdAt: DateTime.now().subtract(const Duration(days: 30)),
      ),
      subtotal: 21.99,
      tax: 2.20,
      discount: 0.0,
      total: 24.19,
      status: OrderStatus.completed,
      paymentMethod: PaymentMethod.card,
      createdAt: DateTime.now().subtract(const Duration(hours: 2)),
      completedAt: DateTime.now().subtract(const Duration(hours: 1, minutes: 30)),
    ),
    Order(
      id: '2',
      items: [
        CartItem(
          product: const Product(
            id: '4',
            name: 'Burger Deluxe',
            description: 'Beef burger with cheese, lettuce, and tomato',
            price: 15.50,
            stock: 12,
            category: 'Main Course',
            barcode: '1234567890126',
          ),
          quantity: 1,
          unitPrice: 15.50,
        ),
      ],
      customer: Customer(
        id: '2',
        name: 'Sarah Johnson',
        email: 'sarah.johnson@email.com',
        phone: '+1-555-0102',
        address: '456 Oak Ave, City, State 12345',
        createdAt: DateTime.now().subtract(const Duration(days: 45)),
      ),
      subtotal: 15.50,
      tax: 1.55,
      discount: 1.50,
      total: 15.55,
      status: OrderStatus.completed,
      paymentMethod: PaymentMethod.cash,
      createdAt: DateTime.now().subtract(const Duration(hours: 4)),
      completedAt: DateTime.now().subtract(const Duration(hours: 3, minutes: 45)),
    ),
  ];

  Future<List<Order>> getAllOrders() async {
    await Future.delayed(const Duration(milliseconds: 500));
    return List.from(_orders);
  }

  Future<Order?> getOrderById(String id) async {
    await Future.delayed(const Duration(milliseconds: 200));
    try {
      return _orders.firstWhere((order) => order.id == id);
    } catch (e) {
      return null;
    }
  }

  Future<List<Order>> getOrdersByCustomer(String customerId) async {
    await Future.delayed(const Duration(milliseconds: 300));
    return _orders.where((order) => order.customer?.id == customerId).toList();
  }

  Future<List<Order>> getOrdersByStatus(OrderStatus status) async {
    await Future.delayed(const Duration(milliseconds: 300));
    return _orders.where((order) => order.status == status).toList();
  }

  Future<List<Order>> getOrdersByDateRange(DateTime start, DateTime end) async {
    await Future.delayed(const Duration(milliseconds: 300));
    return _orders
        .where((order) =>
            order.createdAt.isAfter(start) && order.createdAt.isBefore(end))
        .toList();
  }

  Future<Order> createOrder(Order order) async {
    await Future.delayed(const Duration(milliseconds: 600));
    _orders.add(order);
    return order;
  }

  Future<Order> updateOrder(Order order) async {
    await Future.delayed(const Duration(milliseconds: 400));
    final index = _orders.indexWhere((o) => o.id == order.id);
    if (index != -1) {
      _orders[index] = order;
      return order;
    }
    throw Exception('Order not found');
  }

  Future<void> deleteOrder(String id) async {
    await Future.delayed(const Duration(milliseconds: 300));
    _orders.removeWhere((order) => order.id == id);
  }

  // Analytics methods
  Future<double> getTotalRevenue({DateTime? start, DateTime? end}) async {
    await Future.delayed(const Duration(milliseconds: 300));
    var orders = _orders.where((order) => order.status == OrderStatus.completed);
    
    if (start != null && end != null) {
      orders = orders.where((order) =>
          order.createdAt.isAfter(start) && order.createdAt.isBefore(end));
    }
    
    return orders.fold<double>(0.0, (double sum, Order order) => sum + order.total);
  }

  Future<int> getTotalOrdersCount({DateTime? start, DateTime? end}) async {
    await Future.delayed(const Duration(milliseconds: 300));
    var orders = _orders.where((order) => order.status == OrderStatus.completed);
    
    if (start != null && end != null) {
      orders = orders.where((order) =>
          order.createdAt.isAfter(start) && order.createdAt.isBefore(end));
    }
    
    return orders.length;
  }

  Future<Map<String, int>> getBestSellingProducts({int limit = 10}) async {
    await Future.delayed(const Duration(milliseconds: 400));
    final productSales = <String, int>{};
    
    for (final order in _orders.where((o) => o.status == OrderStatus.completed)) {
      for (final item in order.items) {
        productSales[item.product.name] = 
            (productSales[item.product.name] ?? 0) + item.quantity;
      }
    }
    
    final sortedEntries = productSales.entries.toList()
      ..sort((a, b) => b.value.compareTo(a.value));
    
    return Map.fromEntries(sortedEntries.take(limit));
  }

  Future<List<Map<String, dynamic>>> getDailySales({int days = 30}) async {
    await Future.delayed(const Duration(milliseconds: 400));
    final salesData = <String, double>{};
    final now = DateTime.now();
    
    for (int i = 0; i < days; i++) {
      final date = now.subtract(Duration(days: i));
      final dateKey = '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
      salesData[dateKey] = 0.0;
    }
    
    for (final order in _orders.where((o) => o.status == OrderStatus.completed)) {
      final dateKey = '${order.createdAt.year}-${order.createdAt.month.toString().padLeft(2, '0')}-${order.createdAt.day.toString().padLeft(2, '0')}';
      if (salesData.containsKey(dateKey)) {
        salesData[dateKey] = salesData[dateKey]! + order.total;
      }
    }
    
    return salesData.entries
        .map((entry) => {'date': entry.key, 'sales': entry.value})
        .toList();
  }
}
