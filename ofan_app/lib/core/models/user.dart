import 'dart:convert';

User userFromJson(String str) => User.fromJson(json.decode(str));

class User {
  final int id;
  final String? password;
  final String? username;
  final String? email;
  final String? address;
  final String? phone;
  final String? avatar;
  final String? status;
  final String? fcmToken;
  final String? userToken;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? role;
  final List<String>? permissions;

  const User({
    required this.id,
    this.password,
    this.username,
    this.email,
    this.address,
    this.phone,
    this.avatar,
    this.status,
    this.fcmToken,
    this.userToken,
    this.createdAt,
    this.updatedAt,
    this.role,
    this.permissions,
  });

  factory User.fromJson(Map<String, dynamic> json) => User(
    id: json["id"],
    username: json["name"] ?? json["username"], // Handle both 'name' and 'username'
    email: json["email"],
    address: json["address"],
    phone: json["phone"],
    avatar: json["avatar"],
    status: json["status"] ?? (json["is_active"] == 1 ? "active" : "inactive"), // Handle is_active field
    fcmToken: json["fcm_token"],
    userToken: json["user_token"],
    createdAt: json["created_at"] != null ? DateTime.parse(json["created_at"]) : null,
    updatedAt: json["updated_at"] != null ? DateTime.parse(json["updated_at"]) : null,
    role: json["roles"] != null && json["roles"] is List && (json["roles"] as List).isNotEmpty
        ? (json["roles"] as List).first.toString() // Take first role if roles is array
        : (json["role"]?.toString() ?? "user"), // Fallback to role field or default
    permissions:
        json["permissions"] != null
            ? List<String>.from(json["permissions"].map((x) => x.toString()))
            : null,
  );

  Map<String, dynamic> toJson() => {
    "id": id,
    "name": username, // Send as 'name' to match API expectation
    "username": username, // Keep both for compatibility
    "email": email,
    "address": address,
    "phone": phone,
    "avatar": avatar,
    "status": status,
    "fcm_token": fcmToken,
    "user_token": userToken,
    "created_at": createdAt?.toIso8601String(),
    "updated_at": updatedAt?.toIso8601String(),
    "role": role,
    "roles": role != null ? [role] : [], // Send role as array if needed
    "permissions": permissions ?? [],
  };

  User copyWith({
    required int id,
    String? password,
    String? username,
    String? email,
    String? address,
    String? phone,
    String? avatar,
    String? status,
    String? fcmToken,
    String? userToken,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? role,
    List<String>? permissions
  }) => User(
    id: id,
    password: password ?? this.password,
    username: username ?? this.username,
    email: email ?? this.email,
    address: address ?? this.address,
    phone: phone ?? this.phone,
    avatar: avatar ?? this.avatar,
    status: status ?? this.status,
    fcmToken: fcmToken ?? this.fcmToken,
    userToken: userToken ?? this.userToken,
    createdAt: createdAt ?? this.createdAt,
    updatedAt: updatedAt ?? this.updatedAt,
    role: role ?? this.role,
    permissions: permissions ?? this.permissions
  );

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is User && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;
}
