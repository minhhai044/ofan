import '../models/customer.dart';

class CustomerRepository {
  // Mock data for customers
  static final List<Customer> _customers = [
    Customer(
      id: '1',
      name: 'John Smith',
      email: 'john.smith@email.com',
      phone: '+1-555-0101',
      address: '123 Main St, City, State 12345',
      createdAt: DateTime.now().subtract(const Duration(days: 30)),
      totalSpent: 245.75,
      totalOrders: 8,
    ),
    Customer(
      id: '2',
      name: 'Sarah Johnson',
      email: 'sarah.johnson@email.com',
      phone: '+1-555-0102',
      address: '456 Oak Ave, City, State 12345',
      createdAt: DateTime.now().subtract(const Duration(days: 45)),
      totalSpent: 189.50,
      totalOrders: 6,
    ),
    Customer(
      id: '3',
      name: 'Mike Davis',
      email: 'mike.davis@email.com',
      phone: '+1-555-0103',
      address: '789 Pine Rd, City, State 12345',
      createdAt: DateTime.now().subtract(const Duration(days: 15)),
      totalSpent: 98.25,
      totalOrders: 3,
    ),
    Customer(
      id: '4',
      name: 'Emily Brown',
      email: 'emily.brown@email.com',
      phone: '+1-555-0104',
      address: '321 Elm St, City, State 12345',
      createdAt: DateTime.now().subtract(const Duration(days: 60)),
      totalSpent: 456.80,
      totalOrders: 15,
    ),
    Customer(
      id: '5',
      name: 'David Wilson',
      email: 'david.wilson@email.com',
      phone: '+1-555-0105',
      address: '654 Maple Dr, City, State 12345',
      createdAt: DateTime.now().subtract(const Duration(days: 20)),
      totalSpent: 167.90,
      totalOrders: 5,
    ),
  ];

  Future<List<Customer>> getAllCustomers() async {
    // Simulate API delay
    await Future.delayed(const Duration(milliseconds: 500));
    return List.from(_customers);
  }

  Future<Customer?> getCustomerById(String id) async {
    await Future.delayed(const Duration(milliseconds: 200));
    try {
      return _customers.firstWhere((customer) => customer.id == id);
    } catch (e) {
      return null;
    }
  }

  Future<List<Customer>> searchCustomers(String query) async {
    await Future.delayed(const Duration(milliseconds: 300));
    final lowercaseQuery = query.toLowerCase();
    return _customers
        .where((customer) =>
            customer.name.toLowerCase().contains(lowercaseQuery) ||
            customer.email.toLowerCase().contains(lowercaseQuery) ||
            customer.phone.contains(query))
        .toList();
  }

  Future<Customer> addCustomer(Customer customer) async {
    await Future.delayed(const Duration(milliseconds: 400));
    _customers.add(customer);
    return customer;
  }

  Future<Customer> updateCustomer(Customer customer) async {
    await Future.delayed(const Duration(milliseconds: 400));
    final index = _customers.indexWhere((c) => c.id == customer.id);
    if (index != -1) {
      _customers[index] = customer;
      return customer;
    }
    throw Exception('Customer not found');
  }

  Future<void> deleteCustomer(String id) async {
    await Future.delayed(const Duration(milliseconds: 300));
    _customers.removeWhere((customer) => customer.id == id);
  }

  Future<List<Customer>> getTopCustomers({int limit = 10}) async {
    await Future.delayed(const Duration(milliseconds: 300));
    final sortedCustomers = List<Customer>.from(_customers);
    sortedCustomers.sort((a, b) => b.totalSpent.compareTo(a.totalSpent));
    return sortedCustomers.take(limit).toList();
  }
}
