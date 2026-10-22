import 'dart:convert';
import 'package:absensiart';
import 'package:absensi/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;

class LeavePage extends ConsumerStatefulWidget {
  const LeavePage({super.key});

  @override
  ConsumerState<LeavePage> createState() => _LeavePageState();
}

class _LeavePageState extends ConsumerState<LeavePage> with SingleTickerProviderStateMixin {
  late Future<http.Response>? list;
  late Future<http.Response>? cshList;
  String? selectedValue;


  int quota = 0;
  bool showLeaveButton = false;
  late TabController _tabController;

  Future<http.Response> getLeaveList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse("${Env.api}/api/mobile/leavelist/${other.pegawaiId}");

    final leaveList = http.get(url);

    return leaveList;
  }

  DateTime makeLimit(List<String> start, int l) {
    final now = DateTime.now();

    final limit = DateTime(
      now.year,
      now.month,
      now.day,
      int.parse(start[0]),
      int.parse(start[1]),
    ).add(Duration(minutes: l));

    return limit;
  }

  Map<String, dynamic> getStatusColor(String status) {
    switch (status) {
      case "0":
        return {"status": "pending", "color": Colors.orange};
      case "1":
        return {"status": "approved", "color": Colors.green};
      case "2":
        return {"status": "rejected", "color": Colors.red};
      default:
        return {"status": "pending", "color": Colors.orange};
    }
  }

  Future<http.Response> getCshLeaveList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse("${Env.api}/api/mobile/cshList/${other.pegawaiId}");
    print(url);

    final cshLeaveList = http.get(url);

    return cshLeaveList;
  }

  Future<void> fetch() async {
    setState(() {
      list = null;
    });
    setState(() {
      list = getLeaveList();
    });
  }

  Future<void> fetchCsh() async {
    setState(() {
      cshList = null;
    });
    setState(() {
      cshList = getCshLeaveList();
    });
  }

  void hapusCuti(int index) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text('Konfirmasi'),
        content: Text('Apakah Anda yakin ingin membatalkan cuti ini?'),
        actions: [
          TextButton(
            child: Text('Batal'),
            onPressed: () => Navigator.of(context).pop(),
          ),
          TextButton(
            child: Text('Hapus'),
            onPressed: () {
              setState(() {});
              Navigator.of(context).pop();
            },
          ),
        ],
      ),
    );
  }

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this, initialIndex: 0);

    _tabController.animation?.addListener(() {
      // do something
    });

    fetch();
    fetchCsh();
  }

  String count(String start, String end) {
    DateTime startDate = DateTime.parse(start);
    DateTime endDate = DateTime.parse(end);
    int difference = endDate.difference(startDate).inDays + 1;
    return "${difference.toString()} Hari";
  }

  Map<String, dynamic> makeStatus(int status) {
    if (status == 0) {
      return {
        'text': 'pending',
        'statusStyle': TextStyle(fontSize: 12, color: Colors.orange),
        'counterStyle': TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.bold,
          color: Colors.orange.shade700,
        ),
        'decoration': BoxDecoration(
          gradient: LinearGradient(
            colors: [Colors.orange.shade100, Colors.orange.shade50],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
          borderRadius: const BorderRadius.only(
            topLeft: Radius.circular(8),
            bottomLeft: Radius.circular(8),
          ),
        ),
      };
    }
    if (status == 1) {
      return {
        'text': 'approved',
        'statusStyle': TextStyle(fontSize: 12, color: Colors.blue),
        'counterStyle': TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.bold,
          color: Colors.blue.shade700,
        ),
        'decoration': BoxDecoration(
          gradient: LinearGradient(
            colors: [Colors.blue.shade100, Colors.blue.shade50],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
          borderRadius: const BorderRadius.only(
            topLeft: Radius.circular(8),
            bottomLeft: Radius.circular(8),
          ),
        ),
      };
    }
    if (status == 2) {
      return {
        'text': 'rejected',
        'statusStyle': TextStyle(fontSize: 12, color: Colors.red),
        'counterStyle': TextStyle(
          fontSize: 16,
          fontWeight: FontWeight.bold,
          color: Colors.red.shade700,
        ),
        'decoration': BoxDecoration(
          gradient: LinearGradient(
            colors: [Colors.red.shade100, Colors.red.shade50],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
          borderRadius: const BorderRadius.only(
            topLeft: Radius.circular(8),
            bottomLeft: Radius.circular(8),
          ),
        ),
      };
    }

    return {'text': '', 'statusStyle': null, 'color': null};
  }

  Widget _buildDivider() {
    return Container(
      height: 50,
      width: 1,
      color: Colors.grey.shade300,
      margin: const EdgeInsets.symmetric(horizontal: 8),
    );
  }

  Widget _buildItem(
    String title,
    String value,
    String subtitle,
    Color valueColor,
  ) {
    return Expanded(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(title, style: const TextStyle(fontSize: 14, color: Colors.grey)),
          const SizedBox(height: 6),
          Text(
            value,
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: valueColor,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            subtitle,
            style: const TextStyle(fontSize: 14, color: Colors.grey),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext) {
    final status = ref.read(globalStateProvider).status;
    final schedule = ref.read(globalStateProvider).schedule;
    final config = ref.read(globalStateProvider).config;

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
          title: const Text("Cuti"),
          bottom: TabBar(
            controller: _tabController,

            tabs: [
              Tab(text: 'Cuti'),
              Tab(text: 'Cuti setengah hari'),
            ],
          ),
        ),
        body: TabBarView(
          controller: _tabController,
          children: [
            list != null
                ? FutureBuilder(
                    future: list as Future<http.Response>,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) {
                        return Center(child: CircularProgressIndicator());
                      } else {
                        if (snapshot.hasError) {
                          return Center(child: Text("something went wrong"));
                        } else {
                          final response = snapshot.data!;
                          final data = jsonDecode(response.body);
                          if (data['success'] as bool) {
                            if (quota == 0) {
                              WidgetsBinding.instance.addPostFrameCallback((_) {
                                setState(() {
                                  quota = (data['result']['quota']).floor();
                                });
                              });
                            }
                            return Padding(
                              padding: EdgeInsets.only(left: 20, top: 10),
                              child: Column(
                                children: [
                                  Row(
                                    children: [
                                      data['result']['quota'] >= 0 ? _buildItem(
                                        "Sisa",
                                        "${data['result']['quota']}",
                                        "Hari",
                                        Colors.red,
                                      ) :  _buildItem(
                                        "Sisa",
                                        "0",
                                        "Hari",
                                        Colors.red,
                                      ),
                                      _buildDivider(),
                                      data['result']['quota'] >= 0 ?
                                       _buildItem(
                                        "Terpakai",
                                        "${data['result']['used']}",
                                        "Hari",
                                        Colors.red,
                                      ) :  
                                      _buildItem(
                                        "Terpakai",
                                        "0",
                                        "Hari",
                                        Colors.red,
                                      ),
                                      _buildDivider(),
                                    ],
                                  ),
                                  Expanded(
                                    child: ListView.builder(
                                      itemCount:
                                          (data['result']['list'] as List)
                                              .length,
                                      itemBuilder: (context, index) {
                                        final cuti =
                                            (data['result']['list']
                                                as List)[index];
                                        return Column(
                                          children: [
                                            Container(
                                              margin: EdgeInsets.symmetric(
                                                horizontal: 24,
                                                vertical: 8,
                                              ),
                                              padding: const EdgeInsets.all(12),
                                              child: Row(
                                                children: [
                                                  // Kiri
                                                  Container(
                                                    width: 80,
                                                    padding:
                                                        const EdgeInsets.symmetric(
                                                          vertical: 8,
                                                          horizontal: 4,
                                                        ),
                                                    decoration: makeStatus(
                                                      int.parse(
                                                        cuti['is_status'],
                                                      ),
                                                    )['decoration'],

                                                    child: Column(
                                                      children: [
                                                        Text(
                                                          count(
                                                            cuti['tanggal_request'],
                                                            cuti['tanggal_request_end'],
                                                          ),
                                                          style: makeStatus(
                                                            int.parse(
                                                              cuti['is_status'],
                                                            ),
                                                          )['counterStyle'],
                                                        ),
                                                        const SizedBox(
                                                          height: 4,
                                                        ),
                                                        Text(
                                                          makeStatus(
                                                            int.parse(
                                                              cuti['is_status'],
                                                            ),
                                                          )['text'],
                                                          style: makeStatus(
                                                            int.parse(
                                                              cuti['is_status'],
                                                            ),
                                                          )['statusStyle'],
                                                        ),
                                                      ],
                                                    ),
                                                  ),

                                                  // Garis pemisah vertikal
                                                  Container(
                                                    width: 1,
                                                    height: 60,
                                                    color: Colors.grey.shade300,
                                                    margin:
                                                        const EdgeInsets.symmetric(
                                                          horizontal: 12,
                                                        ),
                                                  ),

                                                  // Kanan
                                                  Expanded(
                                                    flex: 2,
                                                    child: Column(
                                                      crossAxisAlignment:
                                                          CrossAxisAlignment
                                                              .start,
                                                      children: [
                                                        Text(
                                                          cuti['catatan_awal'],
                                                          style: TextStyle(
                                                            fontSize: 16,
                                                            fontWeight:
                                                                FontWeight.w500,
                                                          ),
                                                        ),
                                                        SizedBox(height: 6),
                                                        Row(
                                                          children: [
                                                            Icon(
                                                              Icons
                                                                  .calendar_today,
                                                              size: 16,
                                                              color:
                                                                  Colors.teal,
                                                            ),
                                                            SizedBox(width: 6),
                                                            Text(
                                                              "${cuti['tanggal_request']}  |  ${cuti['tanggal_request_end']}",
                                                            ),
                                                          ],
                                                        ),
                                                      ],
                                                    ),
                                                  ),
                                                ],
                                              ),
                                            ),
                                            Container(
                                              margin: EdgeInsets.symmetric(
                                                horizontal: 24,
                                              ),
                                              width: double.infinity,
                                              height: 1,
                                              color: Colors.grey.shade300,
                                            ),
                                          ],
                                        );
                                      },
                                    ),
                                  ),
                                ],
                              ),
                            );
                          } else {
                            return Center(child: Text("something went wrong"));
                          }
                        }
                      }
                    },
                  )
                : SizedBox(),
            cshList != null
                ? FutureBuilder(
                    future: cshList as Future<http.Response>,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) {
                        return Center(child: CircularProgressIndicator());
                      } else {
                        if (snapshot.hasError) {
                          return Center(child: Text("something went wrong"));
                        } else {
                          final response = snapshot.data!;
                          final data = jsonDecode(response.body);
                          if (data['success'] as bool) {
                            if (quota == 0) {
                              WidgetsBinding.instance.addPostFrameCallback((_) {
                                setState(() {
                                  quota = (data['result']['quota']).floor();
                                });
                              });
                            }
                            return Padding(
                              padding: EdgeInsets.all(16),
                              child: Column(
                                children: [
                                  Row(
                                    children: [
                                      SizedBox(height: 12),
                                      _buildItem(
                                        "Sisa",
                                        "${data['result']['quota']}",
                                        "Hari",
                                        Colors.red,
                                      ),
                                      _buildDivider(),
                                      _buildItem(
                                        "Terpakai",
                                        "${data['result']['used']}",
                                        "Hari",
                                        Colors.black,
                                      ),
                                      _buildDivider(),
                                    ],
                                  ),
                                  SizedBox(height: 12),
                                  Expanded(
                                    child: ListView.builder(
                                      itemCount: List.from(
                                        data['result']['list'],
                                      ).length,
                                      itemBuilder: (context, index) {
                                        final item =
                                            (data['result']['list']
                                                as List)[index];
                                        return Card(
                                          margin: EdgeInsets.symmetric(
                                            horizontal: 12,
                                            vertical: 6,
                                          ),
                                          elevation: 3,
                                          child: Row(
                                            children: [
                                              Expanded(
                                                child: ListTile(
                                                  leading: Icon(
                                                    Icons.event_note,
                                                    color: getStatusColor(
                                                      item['status'],
                                                    )['color'],
                                                  ),
                                                  title: Text(
                                                    "Tanggal: ${item['date']}",
                                                    style: TextStyle(
                                                      fontWeight:
                                                          FontWeight.bold,
                                                    ),
                                                  ),
                                                  subtitle: Text(
                                                    "Reason: ${item['reason']}",
                                                  ),
                                                ),
                                              ),
                                              SizedBox(
                                                child:
                                                    item['type'] ==
                                                        "Cuti setengah hari"
                                                    ? TextButton(
                                                        child: Text(
                                                          "Presensi masuk",
                                                        ),
                                                        onPressed: () {
                                                          if (item['status'] ==
                                                              "1") {
                                                            if (!status
                                                                .signedIn) {
                                                              if (DateTime.now()
                                                                  .isBefore(
                                                                    makeLimit(
                                                                      schedule
                                                                          .start
                                                                          .split(
                                                                            ':',
                                                                          ),
                                                                      config.tolerance +
                                                                          300,
                                                                    ),
                                                                  )) {
                                                                if (DateTime.now().isAfter(
                                                                  makeLimit(
                                                                    schedule
                                                                        .start
                                                                        .split(
                                                                          ':',
                                                                        ),
                                                                    0,
                                                                  ).subtract(
                                                                    Duration(
                                                                      minutes:
                                                                          60,
                                                                    ),
                                                                  ),
                                                                )) {
                                                                  Navigator.pushNamed(
                                                                    context,
                                                                    '/signin',
                                                                    arguments: {
                                                                      'ffocia':
                                                                          false,
                                                                      'csh':
                                                                          true,
                                                                    },
                                                                  );
                                                                } else {
                                                                  ScaffoldMessenger.of(
                                                                    context,
                                                                  ).showSnackBar(
                                                                    const SnackBar(
                                                                      content: Text(
                                                                        "Can not do presence in now",
                                                                      ),
                                                                      duration: Duration(
                                                                        seconds:
                                                                            2,
                                                                      ), // lama tampil
                                                                      backgroundColor:
                                                                          Colors
                                                                              .blue, // warna background
                                                                    ),
                                                                  );
                                                                }
                                                              } else {
                                                                ScaffoldMessenger.of(
                                                                  context,
                                                                ).showSnackBar(
                                                                  const SnackBar(
                                                                    content: Text(
                                                                      "Kamu sudah tidak bisa absen masuk",
                                                                    ),
                                                                    duration: Duration(
                                                                      seconds:
                                                                          2,
                                                                    ), // lama tampil
                                                                    backgroundColor:
                                                                        Colors
                                                                            .blue, // warna background
                                                                  ),
                                                                );
                                                              }
                                                            } else {
                                                              ScaffoldMessenger.of(
                                                                context,
                                                              ).showSnackBar(
                                                                const SnackBar(
                                                                  content: Text(
                                                                    "Kamu sudah absen",
                                                                  ),
                                                                  duration:
                                                                      Duration(
                                                                        seconds:
                                                                            2,
                                                                      ), // lama tampil
                                                                  backgroundColor:
                                                                      Colors
                                                                          .blue, // warna background
                                                                ),
                                                              );
                                                            }
                                                          } else {
                                                            SnackBar(
                                                              content: Text(
                                                                "pengajuan belum disetujui",
                                                              ),
                                                              duration: Duration(
                                                                seconds: 2,
                                                              ), // lama tampil
                                                              backgroundColor:
                                                                  Colors
                                                                      .blue, // warna background
                                                            );
                                                          }
                                                        },
                                                      )
                                                    : SizedBox(),
                                              ),
                                              SizedBox(
                                                child:
                                                    item['type'] ==
                                                        "Cuti pulang"
                                                    ? TextButton(
                                                        child: Text(
                                                          "Presensi pulang",
                                                        ),
                                                        onPressed: () {
                                                          if (item['status'] ==
                                                              "1") {
                                                            Navigator.pushNamed(
                                                              context,
                                                              '/signout',
                                                              arguments: {
                                                                'csh': true,
                                                              },
                                                            );
                                                          } else {
                                                            SnackBar(
                                                              content: Text(
                                                                "Kamu belum bisa absen pulang",
                                                              ),
                                                              duration: Duration(
                                                                seconds: 2,
                                                              ), // lama tampil
                                                              backgroundColor:
                                                                  Colors
                                                                      .blue, // warna background
                                                            );
                                                          }
                                                        },
                                                      )
                                                    : SizedBox(),
                                              ),
                                            ],
                                          ),
                                        );
                                      },
                                    ),
                                  ),
                                ],
                              ),
                            );
                          } else {
                            return Center(child: Text("something went wrong"));
                          }
                        }
                      }
                    },
                  )
                : SizedBox(),
          ],
        ),
        floatingActionButton: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            if (showLeaveButton) ...[
              FloatingActionButton(
                heroTag: "btn2",
                mini: true,
                onPressed: () {
                  Navigator.pushNamed(context, '/leaveapply', arguments: quota);
                },
                child: Icon(Icons.beach_access),
              ),
              SizedBox(height: 8),
              FloatingActionButton(
                heroTag: "btn2",
                mini: true,
                onPressed: () {
                  Navigator.pushNamed(context, '/half_leave', arguments: quota);
                },
                child: Icon(Icons.timer_off),
              ),
              SizedBox(height: 12),
            ],
            FloatingActionButton(
              heroTag: "main",
              onPressed: () {
                setState(() {
                  showLeaveButton = !showLeaveButton;
                });
              },
              child: Icon(Icons.calendar_month),
            ),
          ],
        ),
      ),
    );
  }
}
