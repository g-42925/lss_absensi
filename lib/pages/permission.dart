import 'dart:convert';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';
import 'package:http/http.dart';
import '../env/env.dart';
import './permission_handle.dart';

class PermissionPage extends ConsumerStatefulWidget {
  const PermissionPage({super.key});

  @override
  ConsumerState<PermissionPage> createState() => _PermissionPageState();
}

class _PermissionPageState extends ConsumerState<PermissionPage>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;
  late Future<Response>? sTPL;
  late Future<Response>? pL;

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this, initialIndex: 0);

    _tabController.animation?.addListener(() {
      fetch();
    });

    fetch();
  }

  int countDuration(String x, String y) {
    DateTime startTime = DateTime.parse("2023-01-01 $x:00");
    DateTime endTime = DateTime.parse("2023-01-01 $y:00");

    return endTime.difference(startTime).inHours;
  }

  Future<Response> getPermissionList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse("${Env.api}/api/mobile/gpl/${other.pegawaiId}");

    return http.get(url);
  }

  Future<void> fetch() async {
    setState(() {
      sTPL = null;
      pL = null;
    });
    setState(() {
      sTPL = getShortTimePermissionList();
      pL = getPermissionList();
    });
  }

  Future<Response> getShortTimePermissionList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse("${Env.api}/api/mobile/gstpl/${other.pegawaiId}");

    return http.get(url);
  }

  List<Widget> makeList2(Response reqResponse) {
    final response = jsonDecode(reqResponse.body);
    List<Widget> permissionList = [];
    if (response['success']) {
      response['result'].forEach((r) {
        permissionList.add(
          Card(
            margin: EdgeInsets.only(bottom: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(3),
            ),
            child: Padding(
              padding: EdgeInsets.all(16),
              child: Row(
                children: [
                  Expanded(
                    child: Text(
                      "${r['catatan_awal']} (${r['tanggal_request']} - ${r['tanggal_request_end']})",
                      style: TextStyle(
                        fontSize: 15, // ukuran teks
                        fontWeight: FontWeight.bold, // tebal
                      ),
                    ),
                  ),
                  Container(
                    child: int.parse(r['is_status']) == 1
                        ? Icon(
                            Icons.verified_user,
                            color: Colors.blue,
                            size: 30.0,
                          )
                        : Icon(
                            Icons.access_time,
                            color: Colors.blue,
                            size: 30.0,
                          ),
                  ),
                ],
              ),
            ),
          ),
        );
      });
    }

    return permissionList;
  }

  List<Widget> makeList1(Response reqResponse) {
    final response = jsonDecode(reqResponse.body);
    List<Widget> shortTimePermissionList = [];
    if (response['success'] as bool) {
      response['result'].forEach((r) {
        shortTimePermissionList.add(
          Card(
            margin: EdgeInsets.only(bottom: 16),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(3),
            ),
            child: InkWell(
              onTap: () {
                if (int.parse(r['is_status']) == 1) {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => PermissionHandlePage(
                        requestIzinId: r['request_izin_id'],
                        catatan: r['catatan_awal'],
                        start: r['tanggal_request'],
                        end: r['tanggal_request_end'],
                        jamMasuk: r['r_jam_masuk'],
                        jamKeluar: r['r_jam_keluar'],
                        createdAt: r['created_at'],
                        duration: countDuration(
                          r['r_jam_masuk'],
                          r['r_jam_keluar'],
                        ),
                      ),
                    ),
                  );
                } else {
                  print("belum disetujui");
                }
              },
              child: Padding(
                padding: EdgeInsets.all(16),
                child: Row(
                  children: [
                    Expanded(
                      child: Text(
                        "${r['catatan_awal']} (${r['r_jam_masuk']} - ${r['r_jam_keluar']})",
                        style: TextStyle(
                          fontSize: 15, // ukuran teks
                          fontWeight: FontWeight.bold, // tebal
                        ),
                      ),
                    ),
                    Container(
                      child: int.parse(r['is_status']) == 1
                          ? Icon(
                              Icons.verified_user,
                              color: Colors.blue,
                              size: 30.0,
                            )
                          : Icon(
                              Icons.access_time,
                              color: Colors.blue,
                              size: 30.0,
                            ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      });
    }

    return shortTimePermissionList;
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          leading: IconButton(
            icon: Icon(Icons.arrow_back),
            onPressed: () {
              Navigator.pushNamedAndRemoveUntil(context, '/', (_) => false);
            },
          ),
          title: const Text("Izin"),
          bottom: TabBar(
            tabs: [
              Tab(text: 'Izin Jam'),
            ],
            controller: _tabController,
          ),
        ),
        body: sTPL != null && pL != null
            ? FutureBuilder(
                future: Future.wait([sTPL as Future, pL as Future]),
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) {
                    return Center(child: CircularProgressIndicator());
                  }
                  if (snapshot.hasError) {
                    return Center(child: Text('something went wrong'));
                  } else {
                    final result = snapshot.data as List;
                    return TabBarView(
                      controller: _tabController,
                      children: [
                        Column(
                          children: [
                            Expanded(
                              child: ListView(
                                padding: const EdgeInsets.all(8),
                                children: makeList1(result[0]),
                              ),
                            ),
                          ],
                        ),
                        Column(
                          children: [
                            Expanded(
                              child: ListView(
                                padding: const EdgeInsets.all(8),
                                children: makeList2(result[1]),
                              ),
                            ),
                          ],
                        ),
                      ],
                    );
                  }
                },
              )
            : Container(),
        floatingActionButton: FloatingActionButton(
          onPressed: () {
            if (_tabController.index == 0) {
              Navigator.pushNamed(context, '/short_permission');
            } else {
              Navigator.pushNamed(context, '/long_permission');
            }
          },
          child: const Icon(Icons.add), // ikon tombol
        ),
      ),
    );
  }
}
