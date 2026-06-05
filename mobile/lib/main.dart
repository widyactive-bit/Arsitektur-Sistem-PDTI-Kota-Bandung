import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'screens/login_screen.dart';
import 'screens/dashboard_screen.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  SharedPreferences prefs = await SharedPreferences.getInstance();
  String? token = prefs.getString('auth_token');

  runApp(PsamsApp(isLoggedIn: token != null));
}

class PsamsApp extends StatelessWidget {
  final bool isLoggedIn;

  const PsamsApp({super.key, required this.isLoggedIn});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'PSAMS Mobile',
      debugShowCheckedModeBanner: false,
      themeMode: ThemeMode.dark, // Forced premium dark mode
      darkTheme: ThemeData(
        useMaterial3: true,
        brightness: Brightness.dark,
        colorScheme: ColorScheme.fromSeed(
          seedColor: const Color(0xFFE5B922), // Gold
          brightness: Brightness.dark,
          primary: const Color(0xFFE5B922),
          surface: const Color(0xFF141C26),
          background: const Color(0xFF0B111A),
        ),
        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFF141C26),
          foregroundColor: Colors.white,
          elevation: 0,
        ),
        cardTheme: const CardTheme(
          color: Color(0xFF1C2635),
          elevation: 2,
        ),
      ),
      home: isLoggedIn ? const DashboardScreen() : const LoginScreen(),
    );
  }
}
