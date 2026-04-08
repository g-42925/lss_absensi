import 'dart:convert';

import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

class ShortPermissionPage extends ConsumerStatefulWidget {
  const ShortPermissionPage({super.key});

  @override
  ConsumerState<ShortPermissionPage> createState() =>
      _ShortPemissionPageState();
}

class _ShortPemissionPageState extends ConsumerState<ShortPermissionPage> {
  final TextEditingController reason = TextEditingController();
  final url = Uri.parse("${Env.api}/api/mobile/csh");
  final dateFormat = DateFormat("yyyy-MM-dd");
  final timeFormat = (TimeOfDay t) =>
      "${t.hour.toString().padLeft(2, '0')}:${t.minute.toString().padLeft(2, '0')}";

  DateTime? tanggalMulai;
  DateTime? tanggalSelesai;
  TimeOfDay? jamMulai;
  TimeOfDay? jamSelesai;

  void sendRequest(String companyId, String pegawaiId) async {
    final headers = {"Content-type": "application/json"};

    final params = {
      'company_id': companyId,
      'tanggal_request': dateFormat.format(tanggalMulai!),
      'tanggal_request_end': dateFormat.format(tanggalMulai!),
      'r_jam_masuk': timeFormat(jamMulai!),
      'r_jam_keluar': timeFormat(jamSelesai!),
      'catatan_awal': reason.text,
      'pegawai_id': pegawaiId,
    };

    try {
      await http.post(url, headers: headers, body: jsonEncode(params));
      Navigator.pushReplacementNamed(context, '/');
    } catch (e) {
      print(e);
    }
  }

  Future<void> pilihTanggal(BuildContext context, bool isMulai) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime(2100),
    );
    if (picked != null) {
      setState(() {
        if (isMulai) {
          tanggalMulai = picked;
        } else {
          tanggalSelesai = picked;
        }
      });
    }
  }

  Future<void> pilihJam(BuildContext context, bool isMulai) async {
    final TimeOfDay? picked = await showTimePicker(
      context: context,
      initialTime: TimeOfDay.now(),
    );
    if (picked != null) {
      setState(() {
        if (isMulai) {
          jamMulai = picked;
        } else {
          jamSelesai = picked;
        }
      });
    }
  }

  String formatTanggal(DateTime? date) {
    if (date == null) return "Pilih Tanggal";
    return "${date.day}/${date.month}/${date.year}";
  }

  String formatJam(TimeOfDay? time) {
    if (time == null) return "Pilih Jam";
    return "${time.hour.toString().padLeft(2, '0')}:${time.minute.toString().padLeft(2, '0')}";
  }

  @override
  Widget build(BuildContext context) {
    final globalState = ref.read(globalStateProvider);
    final company = globalState.company;
    final other = globalState.other;

    return Scaffold(
      appBar: AppBar(title: const Text("Izin Jam")),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: GestureDetector(
                    onTap: () => pilihTanggal(context, true),
                    child: InputDecorator(
                      decoration: const InputDecoration(
                        labelText: "Pada Tanggal",
                        border: OutlineInputBorder(),
                      ),
                      child: Text(formatTanggal(tanggalMulai)),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: GestureDetector(
                    onTap: () => pilihJam(context, true),
                    child: InputDecorator(
                      decoration: const InputDecoration(
                        labelText: "Jam Mulai",
                        border: OutlineInputBorder(),
                      ),
                      child: Text(formatJam(jamMulai)),
                    ),
                  ),
                ),
                SizedBox(width: 8),
                Expanded(
                  child: GestureDetector(
                    onTap: () => pilihJam(context, false),
                    child: InputDecorator(
                      decoration: const InputDecoration(
                        labelText: "Jam Selesai",
                        border: OutlineInputBorder(),
                      ),
                      child: Text(formatJam(jamSelesai)),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: reason,
              decoration: const InputDecoration(
                labelText: "Keperluan",
                border: OutlineInputBorder(),
              ),
              maxLines: 3,
            ),
            const Spacer(),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () {
                  sendRequest(company.id, other.pegawaiId);
                  // Aksi kirim
                  // ScaffoldMessenger.of(context).showSnackBar(
                  //   const SnackBar(content: Text("Permintaan dikirim")),
                  // );
                },
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: const Text("Kirim Permintaan"),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
