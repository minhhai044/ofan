import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';

class CacheService {
  static const String _prefix = 'cache_';
  static const Duration _defaultExpiration = Duration(hours: 1);

  static Future<void> set<T>(
    String key,
    T data, {
    Duration? expiration,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    final cacheKey = _prefix + key;
    final expirationTime = DateTime.now().add(expiration ?? _defaultExpiration);
    
    final cacheData = {
      'data': data,
      'expiration': expirationTime.millisecondsSinceEpoch,
    };
    
    await prefs.setString(cacheKey, json.encode(cacheData));
  }

  static Future<T?> get<T>(String key) async {
    final prefs = await SharedPreferences.getInstance();
    final cacheKey = _prefix + key;
    final cacheString = prefs.getString(cacheKey);
    
    if (cacheString == null) return null;
    
    try {
      final cacheData = json.decode(cacheString);
      final expiration = DateTime.fromMillisecondsSinceEpoch(cacheData['expiration']);
      
      if (DateTime.now().isAfter(expiration)) {
        await prefs.remove(cacheKey);
        return null;
      }
      
      return cacheData['data'] as T;
    } catch (e) {
      await prefs.remove(cacheKey);
      return null;
    }
  }

  static Future<void> remove(String key) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_prefix + key);
  }

  static Future<void> clear() async {
    final prefs = await SharedPreferences.getInstance();
    final keys = prefs.getKeys().where((key) => key.startsWith(_prefix));
    for (final key in keys) {
      await prefs.remove(key);
    }
  }

  static Future<bool> exists(String key) async {
    final data = await get(key);
    return data != null;
  }
}
