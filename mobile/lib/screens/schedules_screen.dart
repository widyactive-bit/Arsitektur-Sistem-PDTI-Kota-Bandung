import 'package:flutter/material.dart';
import '../services/api_service.dart';

class SchedulesScreen extends StatefulWidget {
  const SchedulesScreen({super.key});

  @override
  State<SchedulesScreen> createState() => _SchedulesScreenState();
}

class _SchedulesScreenState extends State<SchedulesScreen> {
  final ApiService _apiService = ApiService();
  
  List<dynamic>? _schedules;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadSchedules();
  }

  void _loadSchedules() async {
    final schedules = await _apiService.getSchedules();
    setState(() {
      _schedules = schedules;
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_schedules == null || _schedules!.isEmpty) {
      return const Center(
        child: Text('Belum ada jadwal latihan terdaftar.'),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(20.0),
      itemCount: _schedules!.length,
      itemBuilder: (context, index) {
        final item = _schedules![index];
        
        return Card(
          margin: const EdgeInsets.bottom(16.0),
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.between,
                  children: [
                    Text(
                      '${item['tanggal']}',
                      style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFFE5B922)),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.05),
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Text(
                        '${item['jam_mulai'].substring(0, 5)} - ${item['jam_selesai'].substring(0, 5)}',
                        style: const TextStyle(fontSize: 12),
                      ),
                    )
                  ],
                ),
                const Divider(height: 20),
                Row(
                  children: [
                    const Icon(Icons.home_modern, size: 18, color: Colors.grey),
                    const SizedBox(width: 8),
                    Text(
                      'Klub: ${item['club']['nama_klub']}',
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(Icons.person_outline, size: 18, color: Colors.grey),
                    const SizedBox(width: 8),
                    Text(
                      'Pelatih: ${item['coach']['nama']}',
                      style: const TextStyle(fontSize: 13),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(Icons.location_on_outlined, size: 18, color: Colors.grey),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        'Lokasi: ${item['lokasi']}',
                        style: const TextStyle(fontSize: 13, color: Colors.grey),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
