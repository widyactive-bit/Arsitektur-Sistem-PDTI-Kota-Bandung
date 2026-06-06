import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../services/api_service.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _addressController = TextEditingController();
  final _phoneController = TextEditingController();

  String? _ktpPath;
  String? _kkPath;
  final List<String> _certificatePaths = [];

  bool _isLoading = false;
  bool _obscurePassword = true;
  String? _errorMessage;

  final ApiService _apiService = ApiService();
  final ImagePicker _picker = ImagePicker();

  Future<void> _pickFile(String type) async {
    try {
      final XFile? file = await _picker.pickImage(source: ImageSource.gallery);
      if (file != null) {
        setState(() {
          if (type == 'ktp') {
            _ktpPath = file.path;
          } else if (type == 'kk') {
            _kkPath = file.path;
          } else if (type == 'sertifikat') {
            _certificatePaths.add(file.path);
          }
        });
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Gagal mengambil file: $e')),
      );
    }
  }

  void _handleRegister() async {
    if (!_formKey.currentState!.validate()) return;

    if (_ktpPath == null) {
      setState(() {
        _errorMessage = 'Harap unggah dokumen KTP Anda.';
      });
      return;
    }

    if (_kkPath == null) {
      setState(() {
        _errorMessage = 'Harap unggah dokumen Kartu Keluarga (KK) Anda.';
      });
      return;
    }

    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    final result = await _apiService.register(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      password: _passwordController.text,
      alamat: _addressController.text.trim(),
      noHp: _phoneController.text.trim(),
      ktpPath: _ktpPath!,
      kkPath: _kkPath!,
      sertifikatPaths: _certificatePaths,
    );

    setState(() {
      _isLoading = false;
    });

    if (result['success'] == true && mounted) {
      _showSuccessDialog();
    } else {
      setState(() {
        _errorMessage = result['message'] ?? 'Registrasi gagal. Silakan coba lagi.';
      });
    }
  }

  void _showSuccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        title: const Row(
          children: [
            Icon(Icons.check_circle, color: Colors.green, size: 28),
            SizedBox(width: 8),
            Text('Registrasi Sukses'),
          ],
        ),
        content: const Text(
          'Akun Anda berhasil didaftarkan. Profil Anda dalam status Nonaktif menunggu persetujuan dan verifikasi data (KTP & KK) oleh administrator PSTI Kota Bandung.',
        ),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.pop(context); // Close dialog
              Navigator.pop(context); // Go back to login screen
            },
            child: const Text('Ke Halaman Login', style: TextStyle(color: Color(0xFFE5B922))),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Registrasi Atlet Baru'),
        backgroundColor: const Color(0xFF0B111A),
        foregroundColor: Colors.white,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              if (_errorMessage != null) ...[
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.red.withOpacity(0.15),
                    border: Border.all(color: Colors.red),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    _errorMessage!,
                    style: const TextStyle(color: Colors.redAccent, fontSize: 13),
                  ),
                ),
                const SizedBox(height: 20),
              ],
              TextFormField(
                controller: _nameController,
                decoration: const InputDecoration(
                  labelText: 'Nama Lengkap (Sesuai KTP)',
                  prefixIcon: Icon(Icons.person_outline),
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Nama lengkap wajib diisi';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _emailController,
                keyboardType: TextInputType.emailAddress,
                decoration: const InputDecoration(
                  labelText: 'Email',
                  prefixIcon: Icon(Icons.email_outlined),
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Email wajib diisi';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _passwordController,
                obscureText: _obscurePassword,
                decoration: InputDecoration(
                  labelText: 'Kata Sandi',
                  prefixIcon: const Icon(Icons.lock_outline),
                  border: const OutlineInputBorder(),
                  suffixIcon: IconButton(
                    icon: Icon(
                      _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                    ),
                    onPressed: () {
                      setState(() {
                        _obscurePassword = !_obscurePassword;
                      });
                    },
                  ),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Kata sandi wajib diisi';
                  }
                  if (value.length < 6) {
                    return 'Kata sandi minimal 6 karakter';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _addressController,
                maxLines: 2,
                decoration: const InputDecoration(
                  labelText: 'Alamat Lengkap',
                  prefixIcon: Icon(Icons.home_outlined),
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Alamat wajib diisi';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _phoneController,
                keyboardType: TextInputType.phone,
                decoration: const InputDecoration(
                  labelText: 'No. Handphone (WA Aktif)',
                  prefixIcon: Icon(Icons.phone_android_outlined),
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'No. handphone wajib diisi';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 24),
              const Text(
                'Dokumen Persyaratan',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFFE5B922)),
              ),
              const Divider(color: Color(0xFFE5B922)),
              const SizedBox(height: 8),

              // KTP Upload Card
              Card(
                child: ListTile(
                  leading: const Icon(Icons.badge_outlined, color: Color(0xFFE5B922)),
                  title: const Text('Scan/Foto KTP'),
                  subtitle: Text(_ktpPath == null ? 'Belum diunggah' : 'KTP_terpilih.jpg', style: TextStyle(color: _ktpPath == null ? Colors.grey : Colors.green)),
                  trailing: ElevatedButton.icon(
                    onPressed: () => _pickFile('ktp'),
                    icon: const Icon(Icons.upload_file),
                    label: const Text('Pilih'),
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFE5B922), foregroundColor: Colors.black),
                  ),
                ),
              ),

              // KK Upload Card
              Card(
                child: ListTile(
                  leading: const Icon(Icons.people_outline, color: Color(0xFFE5B922)),
                  title: const Text('Scan/Foto Kartu Keluarga (KK)'),
                  subtitle: Text(_kkPath == null ? 'Belum diunggah' : 'KK_terpilih.jpg', style: TextStyle(color: _kkPath == null ? Colors.grey : Colors.green)),
                  trailing: ElevatedButton.icon(
                    onPressed: () => _pickFile('kk'),
                    icon: const Icon(Icons.upload_file),
                    label: const Text('Pilih'),
                    style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFFE5B922), foregroundColor: Colors.black),
                  ),
                ),
              ),

              const SizedBox(height: 16),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Sertifikat - Sertifikat Pendukung (Opsional)',
                    style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
                  ),
                  IconButton(
                    icon: const Icon(Icons.add_circle_outline, color: Color(0xFFE5B922)),
                    onPressed: () => _pickFile('sertifikat'),
                  ),
                ],
              ),
              const Divider(),
              
              if (_certificatePaths.isEmpty)
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 8.0),
                  child: Text('Belum ada sertifikat ditambahkan', style: TextStyle(fontSize: 12, color: Colors.grey)),
                ),
              
              ...List.generate(_certificatePaths.length, (index) {
                return Card(
                  color: const Color(0xFF162235),
                  child: ListTile(
                    leading: const Icon(Icons.workspace_premium_outlined, color: Color(0xFFE5B922)),
                    title: Text('Sertifikat ${index + 1}'),
                    subtitle: const Text('sertifikat_terpilih.jpg', style: TextStyle(color: Colors.green, fontSize: 12)),
                    trailing: IconButton(
                      icon: const Icon(Icons.delete_outline, color: Colors.red),
                      onPressed: () {
                        setState(() {
                          _certificatePaths.removeAt(index);
                        });
                      },
                    ),
                  ),
                );
              }),

              const SizedBox(height: 32),
              ElevatedButton(
                onPressed: _isLoading ? null : _handleRegister,
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFE5B922),
                  foregroundColor: const Color(0xFF0B111A),
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: _isLoading
                    ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          valueColor: AlwaysStoppedAnimation(Color(0xFF0B111A)),
                        ),
                      )
                    : const Text(
                        'Kirim Registrasi',
                        style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                      ),
              ),
              const SizedBox(height: 16),
            ],
          ),
        ),
      ),
    );
  }
}
