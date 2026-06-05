import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import '../services/api_service.dart';

class AttendanceScreen extends StatefulWidget {
  const AttendanceScreen({super.key});

  @override
  State<AttendanceScreen> createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends State<AttendanceScreen> {
  final ApiService _apiService = ApiService();
  final ImagePicker _picker = ImagePicker();

  bool _isLocating = false;
  bool _isSubmitting = false;
  
  double? _latitude;
  double? _longitude;
  XFile? _selfieFile;
  
  String _statusMessage = 'Dapatkan posisi GPS dan ambil foto selfie untuk melakukan absensi.';
  bool _isCheckedIn = false;

  @override
  void initState() {
    super.initState();
    _checkPermissionAndStatus();
  }

  void _checkPermissionAndStatus() async {
    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      await Geolocator.requestPermission();
    }
  }

  Future<void> _getLocation() async {
    setState(() {
      _isLocating = true;
      _statusMessage = 'Mendeteksi koordinat GPS...';
    });

    try {
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );
      setState(() {
        _latitude = position.latitude;
        _longitude = position.longitude;
        _isLocating = false;
        _statusMessage = 'Lokasi GPS berhasil diperoleh.';
      });
    } catch (e) {
      setState(() {
        _isLocating = false;
        _statusMessage = 'Gagal mendeteksi lokasi GPS: $e';
      });
    }
  }

  Future<void> _takeSelfie() async {
    try {
      final XFile? photo = await _picker.pickImage(
        source: ImageSource.camera,
        preferredCameraDevice: CameraDevice.front,
        maxWidth: 600,
        maxHeight: 600,
        imageQuality: 85,
      );

      if (photo != null) {
        setState(() {
          _selfieFile = photo;
        });
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Gagal membuka kamera: $e')),
      );
    }
  }

  void _submitAttendance(bool checkIn) async {
    if (_latitude == null || _longitude == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Harap dapatkan koordinat lokasi GPS terlebih dahulu.')),
      );
      return;
    }
    if (_selfieFile == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Harap ambil foto selfie PAP lokasi latihan.')),
      );
      return;
    }

    setState(() {
      _isSubmitting = true;
      _statusMessage = 'Mengirim data absensi ke server...';
    });

    // Convert file to mock base64 or pass file name
    final bytes = await _selfieFile!.readAsBytes();
    final base64Image = 'data:image/jpeg;base64,${base64Encode(bytes)}';

    Map<String, dynamic>? response;
    if (checkIn) {
      response = await _apiService.checkIn(_latitude!, _longitude!, base64Image);
    } else {
      response = await _apiService.checkOut(_latitude!, _longitude!, base64Image);
    }

    setState(() {
      _isSubmitting = false;
    });

    if (response != null && response.containsKey('record')) {
      setState(() {
        _isCheckedIn = checkIn;
        _selfieFile = null; // Reset image for next action
        _statusMessage = checkIn 
          ? 'Check In Berhasil dicatat! Selamat berlatih.' 
          : 'Check Out Berhasil! Durasi latihan: ${response!['duration_minutes']} menit.';
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(checkIn ? 'Check-in berhasil!' : 'Check-out berhasil! Durasi dihitung.'),
          backgroundColor: Colors.green,
        ),
      );
    } else {
      setState(() {
        _statusMessage = response?['message'] ?? 'Gagal mengirim data absensi.';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // Geotag Status Card
          Card(
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            child: Padding(
              padding: const EdgeInsets.all(20.0),
              child: Column(
                children: [
                  const Icon(Icons.location_on, color: Color(0xFFE5B922), size: 40),
                  const SizedBox(height: 12),
                  Text(
                    _statusMessage,
                    textAlign: TextAlign.center,
                    style: const TextStyle(fontSize: 13, height: 1.4),
                  ),
                  if (_latitude != null && _longitude != null) ...[
                    const SizedBox(height: 16),
                    Text(
                      'Koordinat: ${_latitude!.toStringAsFixed(6)}, ${_longitude!.toStringAsFixed(6)}',
                      style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.greenAccent),
                    ),
                  ]
                ],
              ),
            ),
          ),
          const SizedBox(height: 24),

          // Camera Action Card
          Card(
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            child: InkWell(
              onTap: _takeSelfie,
              borderRadius: BorderRadius.circular(12),
              child: Padding(
                padding: const EdgeInsets.all(32.0),
                child: Column(
                  children: [
                    const Icon(Icons.camera_alt_outlined, size: 48, color: Colors.grey),
                    const SizedBox(height: 12),
                    Text(
                      _selfieFile == null ? 'Ambil Foto Selfie Latihan' : 'Foto Selfie Siap (Ketuk untuk ganti)',
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                    if (_selfieFile != null) ...[
                      const SizedBox(height: 8),
                      Text(
                        'Berkas: ${_selfieFile!.name}',
                        style: const TextStyle(fontSize: 11, color: Colors.grey),
                      ),
                    ]
                  ],
                ),
              ),
            ),
          ),
          const SizedBox(height: 32),

          // Action Buttons
          ElevatedButton.icon(
            onPressed: _isLocating ? null : _getLocation,
            icon: _isLocating 
              ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2)) 
              : const Icon(Icons.gps_fixed),
            label: const Text('Dapatkan Posisi GPS'),
            style: ElevatedButton.styleFrom(
              padding: const EdgeInsets.symmetric(vertical: 14),
            ),
          ),
          const SizedBox(height: 16),

          Row(
            children: [
              Expanded(
                child: ElevatedButton(
                  onPressed: (_isSubmitting || _isCheckedIn) ? null : () => _submitAttendance(true),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.blueAccent,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                  ),
                  child: const Text('Check In', style: TextStyle(fontWeight: FontWeight.bold)),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: ElevatedButton(
                  onPressed: (_isSubmitting || !_isCheckedIn) ? null : () => _submitAttendance(false),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFE5B922),
                    foregroundColor: Colors.black,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                  ),
                  child: const Text('Check Out', style: TextStyle(fontWeight: FontWeight.bold)),
                ),
              ),
            ],
          )
        ],
      ),
    );
  }
}
