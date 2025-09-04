import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/customer.dart';
import '../repositories/customer_repository.dart';

// Repository provider
final customerRepositoryProvider = Provider<CustomerRepository>((ref) {
  return CustomerRepository();
});

// Customers list provider
final customersProvider = FutureProvider<List<Customer>>((ref) async {
  final repository = ref.read(customerRepositoryProvider);
  return repository.getAllCustomers();
});

// Customer search query provider
final customerSearchQueryProvider = StateProvider<String>((ref) => '');

// Filtered customers provider
final filteredCustomersProvider = FutureProvider<List<Customer>>((ref) async {
  final repository = ref.read(customerRepositoryProvider);
  final searchQuery = ref.watch(customerSearchQueryProvider);
  
  if (searchQuery.isNotEmpty) {
    return repository.searchCustomers(searchQuery);
  } else {
    return repository.getAllCustomers();
  }
});

// Customer by ID provider
final customerByIdProvider = FutureProvider.family<Customer?, String>((ref, id) async {
  final repository = ref.read(customerRepositoryProvider);
  return repository.getCustomerById(id);
});

// Top customers provider
final topCustomersProvider = FutureProvider<List<Customer>>((ref) async {
  final repository = ref.read(customerRepositoryProvider);
  return repository.getTopCustomers(limit: 5);
});

// Selected customer for POS provider
final selectedCustomerProvider = StateProvider<Customer?>((ref) => null);
