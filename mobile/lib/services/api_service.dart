import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  // Use http://10.0.2.2:80 for Android Emulator connecting to localhost
  static const String baseUrl = 'http://10.0.2.2/api'; 

  Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('auth_token');
  }

  // 1. Auth Login
  Future<bool> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/auth/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email, 'password': password}),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', data['token']);
        await prefs.setString('user_name', data['user']['name']);
        await prefs.setString('user_email', data['user']['email']);
        await prefs.setString('user_role', data['user']['role']);
        return true;
      }
      return false;
    } catch (e) {
      print('Login error: $e');
      return false;
    }
  }

  // 2. Auth Logout
  Future<bool> logout() async {
    try {
      final token = await _getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/auth/logout'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      final prefs = await SharedPreferences.getInstance();
      await prefs.clear();
      return response.statusCode == 200;
    } catch (e) {
      print('Logout error: $e');
      return false;
    }
  }

  // 3. Fetch Athlete Profile
  Future<Map<String, dynamic>?> getAthleteProfile() async {
    try {
      final token = await _getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/athlete/profile'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return null;
    } catch (e) {
      print('Fetch profile error: $e');
      return null;
    }
  }

  // 4. Fetch Athlete Stats
  Future<List<dynamic>?> getAthleteStats() async {
    try {
      final token = await _getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/athlete/stats'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return null;
    } catch (e) {
      print('Fetch stats error: $e');
      return null;
    }
  }

  // 5. Fetch Athlete Achievements
  Future<List<dynamic>?> getAchievements() async {
    try {
      final token = await _getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/athlete/achievements'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return null;
    } catch (e) {
      print('Fetch achievements error: $e');
      return null;
    }
  }

  // 6. Fetch Schedules
  Future<List<dynamic>?> getSchedules() async {
    try {
      final token = await _getToken();
      final response = await http.get(
        Uri.parse('$baseUrl/schedules'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return null;
    } catch (e) {
      print('Fetch schedules error: $e');
      return null;
    }
  }

  // 7. Attendance Check In
  Future<Map<String, dynamic>?> checkIn(double lat, double lng, String selfieBase64) async {
    try {
      final token = await _getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/attendance/checkin'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'latitude': lat,
          'longitude': lng,
          'selfie': selfieBase64,
        }),
      );

      return jsonDecode(response.body);
    } catch (e) {
      print('Check-in error: $e');
      return {'message': 'Gagal menghubungkan ke server.'};
    }
  }

  // 8. Attendance Check Out
  Future<Map<String, dynamic>?> checkOut(double lat, double lng, String selfieBase64) async {
    try {
      final token = await _getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/attendance/checkout'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          'latitude': lat,
          'longitude': lng,
          'selfie': selfieBase64,
        }),
      );

      return jsonDecode(response.body);
    } catch (e) {
      print('Check-out error: $e');
      return {'message': 'Gagal menghubungkan ke server.'};
    }
  }

  // 9. Fetch AI Analytics Narrative Recommendation
  Future<Map<String, dynamic>?> getAiAnalytics() async {
    try {
      final token = await _getToken();
      final response = await http.post(
        Uri.parse('$baseUrl/athlete/ai-analytics'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
      return null;
    } catch (e) {
      print('Fetch AI analytics error: $e');
      return null;
    }
  }
}
