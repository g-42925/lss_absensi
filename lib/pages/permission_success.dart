import 'package:flutter/material.dart';

class PermissionSuccessPage extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.all(20.0),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Gambar ilustrasi (gunakan asset Anda sendiri)
                SizedBox(
                  height: 359,
                  child: Image.asset(
                    fit: BoxFit.fitHeight,
                    'assets/enjoy.png', // ganti dengan ilustrasi Anda
                  ),
                ),
                const SizedBox(height: 24),

                // Judul
                const Text(
                  "Permintaan Dikirim!",
                  style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 12),

                // Deskripsi
                const Text(
                  "Permintaan Izin/Cuti Anda berhasil dibuat. Saat ini permintaan Anda sedang dalam proses tinjauan",
                  style: TextStyle(fontSize: 14, color: Colors.black54),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 40),

                // Tombol
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.teal,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    onPressed: () {
                      Navigator.pushNamed(context, '/permission');
                    },
                    child: const Text(
                      "Ya, Mengerti",
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
