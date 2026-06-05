import 'package:flutter/material.dart';
import 'attendance_screen.dart';
import 'stats_screen.dart';
import 'schedules_screen.dart';
import 'profile_screen.dart';
import '../services/api_service.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _selectedIndex = 0;
  final ApiService _apiService = ApiService();
  
  Map<String, dynamic>? _profile;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadProfile();
  }

  void _loadProfile() async {
    final profile = await _apiService.getAthleteProfile();
    if (profile != null) {
      setState(() {
        _profile = profile;
        _isLoading = false;
      });
    } else {
      setState(() {
        _isLoading = false;
      });
    }
  }

  Widget _buildHomeTab() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    final athlete = _profile != null ? _profile!['athlete'] : null;
    final score = _profile != null ? _profile!['ranking_score'] : '0.00';

    return SingleChildScrollView(
      padding: const EdgeInsets.all(20.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Welcome Card
          Card(
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            child: Padding(
              padding: const EdgeInsets.all(20.0),
              child: Row(
                children: [
                  CircleAvatar(
                    radius: 30,
                    backgroundColor: const Color(0xFFE5B922),
                    child: Text(
                      athlete != null ? athlete['nama_lengkap'][0] : 'A',
                      style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.black),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          athlete != null ? athlete['nama_lengkap'] : 'Nama Atlet',
                          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                        ),
                        Text(
                          athlete != null ? 'NIA: ${athlete['nomor_induk_atlet']}' : 'NIA: -',
                          style: const TextStyle(color: Colors.grey, fontSize: 13),
                        ),
                        Text(
                          athlete != null ? athlete['klub'] : 'Klub Takraw',
                          style: const TextStyle(color: Color(0xFFE5B922), fontSize: 13, fontWeight: FontWeight.w600),
                        ),
                      ],
                    ),
                  )
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),
          
          // Performance Rank Card
          Card(
            color: const Color(0xFF152232),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
              side: const BorderSide(color: Color(0xFFE5B922), width: 0.5),
            ),
            child: Padding(
              padding: const EdgeInsets.all(20.0),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.between,
                children: [
                  const Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Skor PSAMS Atlet',
                        style: TextStyle(fontSize: 14, color: Colors.grey, fontWeight: FontWeight.bold),
                      ),
                      SizedBox(height: 4),
                      Text(
                        'Performa Analitis Bulanan',
                        style: TextStyle(fontSize: 11, color: Colors.grey),
                      ),
                    ],
                  ),
                  Text(
                    '$score',
                    style: const TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: Color(0xFFE5B922)),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),
          
          const Text(
            'Layanan & Taktis',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 12),

          // Shortcut Grid
          GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: 2,
            crossAxisSpacing: 16,
            mainAxisSpacing: 16,
            childAspectRatio: 1.4,
            children: [
              _buildShortcutCard(
                icon: Icons.location_on_outlined,
                label: 'Absensi GPS',
                color: Colors.blueAccent,
                onTap: () => setState(() => _selectedIndex = 1),
              ),
              _buildShortcutCard(
                icon: Icons.bar_chart_outlined,
                label: 'Statistik Atlet',
                color: const Color(0xFFE5B922),
                onTap: () => setState(() => _selectedIndex = 2),
              ),
              _buildShortcutCard(
                icon: Icons.calendar_today_outlined,
                label: 'Jadwal Latihan',
                color: Colors.greenAccent,
                onTap: () => setState(() => _selectedIndex = 3),
              ),
              _buildShortcutCard(
                icon: Icons.person_outline,
                label: 'Profil Pengguna',
                color: Colors.purpleAccent,
                onTap: () => setState(() => _selectedIndex = 4),
              ),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildShortcutCard({
    required IconData icon,
    required String label,
    required Color color,
    required VoidCallback onTap,
  }) {
    return Card(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(8),
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Icon(icon, color: color, size: 28),
              const SizedBox(height: 12),
              Text(
                label,
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
              )
            ],
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final tabs = [
      _buildHomeTab(),
      const AttendanceScreen(),
      const StatsScreen(),
      const SchedulesScreen(),
      const ProfileScreen(),
    ];

    final titles = [
      'Dashboard',
      'Absensi GPS',
      'Analisis Statistik',
      'Jadwal Latihan',
      'Profil Atlet',
    ];

    return Scaffold(
      appBar: AppBar(
        title: Text(titles[_selectedIndex]),
        centerTitle: true,
      ),
      body: tabs[_selectedIndex],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _selectedIndex,
        onTap: (index) {
          setState(() {
            _selectedIndex = index;
          });
        },
        type: BottomNavigationBarType.fixed,
        backgroundColor: const Color(0xFF141C26),
        selectedItemColor: const Color(0xFFE5B922),
        unselectedItemColor: Colors.grey,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.dashboard_outlined), label: 'Home'),
          BottomNavigationBarItem(icon: Icon(Icons.location_on_outlined), label: 'Absen'),
          BottomNavigationBarItem(icon: Icon(Icons.bar_chart_outlined), label: 'Statistik'),
          BottomNavigationBarItem(icon: Icon(Icons.calendar_today_outlined), label: 'Jadwal'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'Profil'),
        ],
      ),
    );
  }
}
