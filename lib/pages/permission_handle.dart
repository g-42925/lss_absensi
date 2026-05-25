import 'dart:convert';
import 'dart:async';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';
import 'package:http/http.dart';
import '../env/env.dart';

class PermissionHandlePage extends ConsumerStatefulWidget {
  final String createdAt;
  final int duration;
  final String start;
  final String end;
  final String jamMasuk;
  final String jamKeluar;
  final String catatan;
  final String requestIzinId;

  const PermissionHandlePage({
    super.key,
    required this.createdAt,
    required this.duration,
    required this.start,
    required this.end,
    required this.jamMasuk,
    required this.jamKeluar,
    required this.catatan,
    required this.requestIzinId,
  });

  @override
  ConsumerState<PermissionHandlePage> createState() =>
      _PermissionHandlePageState();
}

class _PermissionHandlePageState extends ConsumerState<PermissionHandlePage> {
  Future<Response>? response;

  @override
  void initState() {
    super.initState();
  }

  Future<Response> setLeave(String id) async {
    final DateTime sekarang = DateTime.now();
    final String jamMenit = DateFormat('HH:mm').format(sekarang);
    final Uri url = Uri.parse("${Env.api}/api/mobile/setleave");
    final headers = {"Content-type": "application/json"};

    final Map<String, String> params = {
      'r_absen_keluar': jamMenit,
      'request_izin_id': id,
    };

    return http.post(url, headers: headers, body: jsonEncode(params)).timeout(
      const Duration(milliseconds: 100)
    );
  }

  Future<Response> setComeBack(String id) async {
    final DateTime sekarang = DateTime.now();
    final String jamMenit = DateFormat('HH:mm').format(sekarang);
    final Uri url = Uri.parse("${Env.api}/api/mobile/setcomeback");
    final headers = {"Content-type": "application/json"};

    final Map<String, String> params = {
      'r_absen_masuk': jamMenit,
      'request_izin_id': id,
    };

    return http.post(url, headers: headers, body: jsonEncode(params));
  }

  @override
  Widget build(BuildContext context) {
    final Other detail = ref.read(globalStateProvider).other;
    final List<String> btnIds = ref.watch(globalStateProvider).history;

    return Scaffold(
      appBar: AppBar(
        title: const Text("Status Izin"),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 16),
            Row(
              children: [
                const CircleAvatar(
                  radius: 28,
                  backgroundImage: NetworkImage(
                    "https://via.placeholder.com/129", // Ganti foto user
                  ),
                ),
                SizedBox(width: 16),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      detail.namaPegawai,
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                      ),
                    ),
                  ],
                ),
              ],
            ),
            SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  "Detail Permintaan",
                  style: TextStyle(fontWeight: FontWeight.w600),
                ),
                Text(widget.createdAt, style: TextStyle(color: Colors.grey)),
              ],
            ),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                border: Border.all(color: Colors.grey.shade300),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        "Durasi",
                        style: TextStyle(fontWeight: FontWeight.w500),
                      ),
                      Row(
                        children: [
                          Icon(Icons.access_time, size: 18),
                          SizedBox(width: 4),
                          Text("${widget.duration} Jam"),
                        ],
                      ),
                    ],
                  ),
                  Divider(),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text("Mulai"),
                          Text(
                            widget.jamMasuk,
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 16,
                            ),
                          ),
                          Text(widget.start),
                        ],
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text("Selesai"),
                          Text(
                            widget.jamKeluar,
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 16,
                            ),
                          ),
                          Text(widget.end),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 12),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(Icons.chat_bubble_outline, size: 18),
                      SizedBox(width: 4),
                      Text("Keterangan"),
                    ],
                  ),
                  SizedBox(height: 4),
                  Text(
                    widget.catatan,
                    style: TextStyle(fontWeight: FontWeight.w500),
                  ),
                ],
              ),
            ),
            SizedBox(height: 6),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () async {
                  final now = DateTime.now();
                  final parsed = DateFormat("HH:mm").parse(widget.jamMasuk);
                  final jamMasukToday = DateTime(
                    now.year,
                    now.month,
                    now.day,
                    parsed.hour,
                    parsed.minute,
                  );
                  if (!btnIds.contains("l-${widget.requestIzinId}")) {
                    if (now.isBefore(jamMasukToday)) {
                      showModalBottomSheet(
                        context: context,
                        isScrollControlled: true,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.vertical(
                            top: Radius.circular(20),
                          ),
                        ),
                        builder: (context) {
                          return FractionallySizedBox(
                            heightFactor: 0.5, // setengah layar
                            child: Padding(
                              padding: const EdgeInsets.all(20),
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    Icons.error_outline,
                                    color: Colors.red,
                                    size: 60,
                                  ),
                                  SizedBox(height: 20),
                                  Text(
                                    'Kamu belum boleh meninggalkan kantor',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(
                                      fontSize: 20,
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  SizedBox(height: 8),
                                  Text(
                                    'Mohon tunggu sampai waktu nya tiba',
                                    textAlign: TextAlign.center,
                                    style: TextStyle(
                                      fontSize: 16,
                                      color: Colors.black54,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      );
                    } 
                    else {
                      try{
                        await setLeave(widget.requestIzinId);
                        ref.read(globalStateProvider.notifier).addHistory(
                          "l-${widget.requestIzinId}"
                        );
                      }
                      on TimeoutException catch(err){
                        showModalBottomSheet(
                          context: context,
                          backgroundColor: Colors.transparent,
                          builder: (_) => Container(
                            margin: EdgeInsets.all(16),
                            padding: EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.red,
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Row(
                              children: [
                                Icon(Icons.error, color: Colors.white),
                                SizedBox(width: 10),
                                Expanded(
                                  child: Text(
                                    "Request timeout",
                                    style: TextStyle(color: Colors.white),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      }
                      catch(err){
                        showModalBottomSheet(
                          context: context,
                          backgroundColor: Colors.transparent,
                          builder: (_) => Container(
                            margin: EdgeInsets.all(16),
                            padding: EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.red,
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Row(
                              children: [
                                Icon(Icons.error, color: Colors.white),
                                SizedBox(width: 10),
                                Expanded(
                                  child: Text(
                                    "Request timeout or something went wrong",
                                    style: TextStyle(color: Colors.white),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      }
                      final future = setLeave(widget.requestIzinId);
                      setState(() {
                        response = future;
                      });

                      try {
                        await future;
                        ref.read(globalStateProvider.notifier).addHistory(
                          "l-${widget.requestIzinId}"
                        );
                      } catch (err) {
                        // do something
                      }
                    }
                  } else {
                    // cant click more
                  }
                },
                label: const Text(
                  'Meninggalkan kantor',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 14,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: !btnIds.contains("l-${widget.requestIzinId}")
                      ? Colors.red
                      : Colors.blue,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 20,
                    vertical: 12,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(6),
                  ),
                  elevation: 0, // tanpa shadow
                ),
              ),
            ),
            SizedBox(height: 6),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: () async {
                  if (btnIds.contains("l-${widget.requestIzinId}")) {
                    if (!btnIds.contains("a-${widget.requestIzinId}")) {
                      final future = setComeBack(widget.requestIzinId);
                      setState(() {
                        response = future;
                      });

                      try {
                        await future;
                        ref
                            .read(globalStateProvider.notifier)
                            .addHistory("a-${widget.requestIzinId}");
                      } catch (err) {
                        // do something
                      }
                    } else {
                      // do something
                    }
                  } else {
                    // do something
                  }
                },
                label: const Text(
                  'Sudah kembali',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 14,
                    fontWeight: FontWeight.w500,
                  ),
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: !btnIds.contains("a-${widget.requestIzinId}")
                      ? Colors.red
                      : Colors.blue, // hijau tosca
                  padding: const EdgeInsets.symmetric(
                    horizontal: 20,
                    vertical: 12,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(6),
                  ),
                  elevation: 0, // tanpa shadow
                ),
              ),
            ),
            SizedBox(height: 24),
            Container(
              child: response != null ? FutureBuilder(
                future: response,
                builder: (context, snapshot) {
                  if (snapshot.connectionState ==ConnectionState.waiting) {
                    return Center(child: CircularProgressIndicator());
                  } 
                  else if (snapshot.hasError) {
                    return Center(child: Text("Error: ${snapshot.error}"));
                  }
                  else {
                    return Container();
                  }
                },
              )
              : Container(),
            ),
          ],
        ),
      ),
    );
  }
}
