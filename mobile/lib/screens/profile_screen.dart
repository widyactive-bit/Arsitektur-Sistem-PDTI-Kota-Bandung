import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'login_screen.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final ApiService _apiService = ApiService();
  
  Map<String, dynamic>? _profile;
  List<dynamic>? _achievements;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadProfileAndAchievements();
  }

  void _loadProfileAndAchievements() async {
    final profile = await _apiService.getAthleteProfile();
    final achievements = await _apiService.getAchievements();

    setState(() {
      _profile = profile;
      _achievements = achievements;
      _isLoading = false;
    });
  }

  void _handleLogout() async {
    final success = await _apiService.logout();
    if (success && mounted) {
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
        (route) => false,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    final athlete = _profile != null ? _profile!['athlete'] : null;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Biodata list
          if (athlete != null) ...[
            const Text(
              'Detail Biodata Atlet',
              style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
            ),
            const SizedBox(height: 12),
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  children: [
                    _buildInfoRow('NIK', '${athlete['nik']}'),
                    _buildInfoRow('Tempat, Tgl Lahir', '${athlete['tempat_lahir']}, ${athlete['tanggal_lahir']}'),
                    _buildInfoRow('Jenis Kelamin', '${athlete['jenis_kelamin']}'),
                    _buildInfoRow('No. HP', '${athlete['no_hp']}'),
                    _buildInfoRow('Alamat', '${athlete['alamat']}'),
                    _buildInfoRow('Tinggi / Berat', '${athlete['tinggi_badan']} cm / ${athlete['berat_badan']} kg'),
                    _buildInfoRow('Kategori Posisi', '${athlete['kelas_tanding']}'),
                    _buildInfoRow('Tingkat (Sabuk)', '${athlete['sabuk']}'),
                  ],
                ),
              ),
            ),
          ],
          const SizedBox(height: 24),

          // Achievements list
          const Text(
            'Riwayat Prestasi & Medali',
            style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
          ),
          const SizedBox(height: 12),
          
          if (_achievements == null || _achievements!.isEmpty)
            const Card(
              child: Padding(
                padding: EdgeInsets.all(16.0),
                child: Text('Belum ada riwayat prestasi tercatat.', style: TextStyle(color: Colors.grey, fontSize: 13)),
              ),
            )
          else
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: _achievements!.length,
              itemBuilder: (context, index) {
                final ach = _achievements![index];
                final hasMedal = ach['medali'] != null;
                
                return Card(
                  margin: const EdgeInsets.bottom(12.0),
                  child: ListTile(
                    leading: Icon(
                      Icons.emoji_events,
                      color: ach['medali'] == 'Emas' 
                        ? const Color(0xFFE5B922) 
                        : (ach['medali'] == 'Perak' ? Colors.grey : const Color(0xFFC2410C)),
                    ),
                    title: Text(
                      '${ach['nama_kejuaraan']}',
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                    ),
                    subtitle: Text('${ach['hasil']} (${ach['tanggal']})', style: const TextStyle(fontSize: 11)),
                    trailing: Text(
                      hasMedal ? '${ach['medali']}' : 'Ikut Serta',
                      style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFFE5B922)),
                    ),
                  ),
                );
              },
            ),
          
          const SizedBox(height: 32),

          // Logout Button
          ElevatedButton.icon(
            onPressed: _handleLogout,
            icon: const Icon(Icons.logout),
            label: const Text('Keluar Aplikasi'),
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.redAccent,
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(vertical: 16),
            ),
          )
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 130,
            child: Text(
              label,
              style: const TextStyle(color: Colors.grey, fontSize: 13),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
            ),
          ),
        ],
      ),
    );
  }
}
