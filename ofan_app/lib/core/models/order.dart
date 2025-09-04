import 'cart_item.dart';
import 'customer.dart';

enum OrderStatus { pending, completed, cancelled, refunded }

enum PaymentMethod { cash, card, mobile, other }

class Order {
  final String id;
  final List<CartItem> items;
  final Customer? customer;
  final double subtotal;
  final double tax;
  final double discount;
  final double total;
  final OrderStatus status;
  final PaymentMethod paymentMethod;
  final DateTime createdAt;
  final DateTime? completedAt;
  final String? notes;

  const Order({
    required this.id,
    required this.items,
    this.customer,
    required this.subtotal,
    required this.tax,
    required this.discount,
    required this.total,
    required this.status,
    required this.paymentMethod,
    required this.createdAt,
    this.completedAt,
    this.notes,
  });

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'] as String,
      items: (json['items'] as List)
          .map((item) => CartItem.fromJson(item as Map<String, dynamic>))
          .toList(),
      customer: json['customer'] != null
          ? Customer.fromJson(json['customer'] as Map<String, dynamic>)
          : null,
      subtotal: (json['subtotal'] as num).toDouble(),
      tax: (json['tax'] as num).toDouble(),
      discount: (json['discount'] as num).toDouble(),
      total: (json['total'] as num).toDouble(),
      status: OrderStatus.values.firstWhere(
        (e) => e.name == json['status'],
        orElse: () => OrderStatus.pending,
      ),
      paymentMethod: PaymentMethod.values.firstWhere(
        (e) => e.name == json['paymentMethod'],
        orElse: () => PaymentMethod.cash,
      ),
      createdAt: DateTime.parse(json['createdAt'] as String),
      completedAt: json['completedAt'] != null
          ? DateTime.parse(json['completedAt'] as String)
          : null,
      notes: json['notes'] as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'items': items.map((item) => item.toJson()).toList(),
      'customer': customer?.toJson(),
      'subtotal': subtotal,
      'tax': tax,
      'discount': discount,
      'total': total,
      'status': status.name,
      'paymentMethod': paymentMethod.name,
      'createdAt': createdAt.toIso8601String(),
      'completedAt': completedAt?.toIso8601String(),
      'notes': notes,
    };
  }

  Order copyWith({
    String? id,
    List<CartItem>? items,
    Customer? customer,
    double? subtotal,
    double? tax,
    double? discount,
    double? total,
    OrderStatus? status,
    PaymentMethod? paymentMethod,
    DateTime? createdAt,
    DateTime? completedAt,
    String? notes,
  }) {
    return Order(
      id: id ?? this.id,
      items: items ?? this.items,
      customer: customer ?? this.customer,
      subtotal: subtotal ?? this.subtotal,
      tax: tax ?? this.tax,
      discount: discount ?? this.discount,
      total: total ?? this.total,
      status: status ?? this.status,
      paymentMethod: paymentMethod ?? this.paymentMethod,
      createdAt: createdAt ?? this.createdAt,
      completedAt: completedAt ?? this.completedAt,
      notes: notes ?? this.notes,
    );
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is Order && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;
}
