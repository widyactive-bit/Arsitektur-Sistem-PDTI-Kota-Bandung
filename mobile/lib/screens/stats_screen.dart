import 'package:flutter/material.dart';
import '../services/api_service.dart';

class StatsScreen extends StatefulWidget {
  const StatsScreen({super.key});

  @override
  State<StatsScreen> createState() => _StatsScreenState();
}

class _StatsScreenState extends State<StatsScreen> {
  final ApiService _apiService = ApiService();
  
  List<dynamic>? _statsList;
  bool _isLoading = true;
  
  bool _isGeneratingAi = false;
  Map<String, dynamic>? _aiAnalysis;

  @override
  void initState() {
    super.initState();
    _loadStats();
  }

  void _loadStats() async {
    final stats = await _apiService.getAthleteStats();
    setState(() {
      _statsList = stats;
      _isLoading = false;
    });
  }

  void _generateAiAnalysis() async {
    setState(() {
      _isGeneratingAi = true;
    });

    final analysis = await _apiService.getAiAnalytics();
    
    setState(() {
      _aiAnalysis = analysis;
      _isGeneratingAi = false;
    });
  }

  Widget _buildMetricsSection(Map<String, dynamic> stat) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Technical
        const Text(
          'Metrik Teknik',
          style: TextStyle(fontWeight: FontWeight.bold, color: Color(0xFFE5B922), fontSize: 14),
        ),
        const SizedBox(height: 8),
        _buildStatRow('Tendangan Sila / Kura', stat['tendangan'].toDouble()),
        _buildStatRow('Smash Salto / Hadangan Dada', stat['pukulan'].toDouble()),
        _buildStatRow('Akurasi Penempatan Bola', stat['akurasi'].toDouble()),
        _buildStatRow('Kecepatan Gerak', stat['kecepatan'].toDouble()),
        
        const SizedBox(height: 20),

        // Physical
        const Text(
          'Metrik Fisik',
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.blueAccent, fontSize: 14),
        ),
        const SizedBox(height: 8),
        _buildStatRow('Daya Tahan (Endurance)', stat['endurance'].toDouble()),
        _buildStatRow('Kelincahan (Agility)', stat['agility'].toDouble()),
        _buildStatRow('Kelenturan (Flexibility)', stat['flexibility'].toDouble()),
        _buildStatRow('Kekuatan Otot (Strength)', stat['strength'].toDouble()),

        const SizedBox(height: 20),

        // Mental
        const Text(
          'Metrik Mental & Karakter',
          style: TextStyle(fontWeight: FontWeight.bold, color: Colors.greenAccent, fontSize: 14),
        ),
        const SizedBox(height: 8),
        _buildStatRow('Disiplin Latihan', stat['disiplin'].toDouble()),
        _buildStatRow('Fokus Bertanding', stat['fokus'].toDouble()),
        _buildStatRow('Kepemimpinan (Leadership)', stat['leadership'].toDouble()),
      ],
    );
  }

  Widget _buildStatRow(String label, double value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.between,
            children: [
              Text(label, style: const TextStyle(fontSize: 12)),
              Text(
                value.toStringAsFixed(1),
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
              ),
            ],
          ),
          const SizedBox(height: 4),
          LinearProgressIndicator(
            value: value / 100,
            backgroundColor: Colors.white.withOpacity(0.05),
            valueColor: AlwaysStoppedAnimation(
              value > 80 ? Colors.greenAccent : (value > 60 ? const Color(0xFFE5B922) : Colors.redAccent),
            ),
          )
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_statsList == null || _statsList!.isEmpty) {
      return const Center(
        child: Text('Data statistik evaluasi belum tersedia.'),
      );
    }

    final latestStat = _statsList!.last;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Header monthly date card
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.between,
                children: [
                  const Text('Periode Evaluasi Terakhir:', style: TextStyle(fontWeight: FontWeight.bold)),
                  Text(
                    '${latestStat['record_date']}',
                    style: const TextStyle(color: Color(0xFFE5B922), fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),

          // Custom metrics section
          _buildMetricsSection(latestStat),
          const SizedBox(height: 32),

          // AI Analytics Box
          Card(
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
              side: const BorderSide(color: Color(0xFFE5B922), width: 0.5),
            ),
            child: Padding(
              padding: const EdgeInsets.all(20.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.psychology, color: Color(0xFFE5B922), size: 28),
                      SizedBox(width: 8),
                      Text(
                        'AI Recommendation (OpenAI)',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  if (_aiAnalysis == null) ...[
                    const Text(
                      'Dapatkan narasi kelebihan, kekurangan, dan rekomendasi program latihan sepak takraw terarah dari AI.',
                      style: TextStyle(fontSize: 12, color: Colors.grey, height: 1.4),
                    ),
                    const SizedBox(height: 20),
                    ElevatedButton(
                      onPressed: _isGeneratingAi ? null : _generateAiAnalysis,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFFE5B922),
                        foregroundColor: Colors.black,
                      ),
                      child: _isGeneratingAi
                          ? const SizedBox(
                              width: 18,
                              height: 18,
                              child: CircularProgressIndicator(strokeWidth: 2, valueColor: AlwaysStoppedAnimation(Colors.black)),
                            )
                          : const Text('Generate Analisis AI', style: TextStyle(fontWeight: FontWeight.bold)),
                    ),
                  ] else ...[
                    Text(
                      'Metode Analisis: ${_aiAnalysis!['engine']}',
                      style: const TextStyle(fontSize: 10, color: Colors.grey, fontStyle: FontStyle.italic),
                    ),
                    const SizedBox(height: 12),
                    Text(
                      _aiAnalysis!['analysis'],
                      style: const TextStyle(fontSize: 12, height: 1.5),
                    ),
                    const SizedBox(height: 16),
                    OutlinedButton(
                      onPressed: _generateAiAnalysis,
                      child: const Text('Perbarui Analisis'),
                    )
                  ]
                ],
              ),
            ),
          )
        ],
      ),
    );
  }
}
