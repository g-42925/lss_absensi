import 'dart:convert';

import 'package:absensinv/env.dart';
import 'package:absensiroviders/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;

class ExceptionPage extends ConsumerStatefulWidget {
  const ExceptionPage({super.key});

  @override
  ConsumerState<ExceptionPage> createState() => _ExceptionPageState();
}

class _ExceptionPageState extends ConsumerState<ExceptionPage> {
  late Future<http.Response>? list;
  DateTime? selectedDate;

  Future<http.Response> getLeaveList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse("${Env.api}/api/mobile/excList/${other.pegawaiId}");

    return http.get(url);
  }

  Future<void> fetch() async {
    setState(() {
      list = null;
    });
    setState(() {
      list = getLeaveList();
    });
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

  Future<void> refresh() async {
    await fetch();
  }

  @override
  void initState() {
    super.initState();
    fetch();
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

  @override
  Widget build(BuildContext context) {
    final status = ref.read(globalStateProvider).status;
    final schedule = ref.read(globalStateProvider).schedule;
    final config = ref.read(globalStateProvider).config;

    return Scaffold(
      appBar: AppBar(title: Text("Pengecualian")),
      body: list != null
          ? FutureBuilder(
              future: list,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Center(child: CircularProgressIndicator());
                }
                if (snapshot.hasError) {
                  return Center(child: Text("something went wrong"));
                } else {
                  final response = snapshot.data!;
                  final data = jsonDecode(response.body);
                  if (data['success'] as bool) {
                    return ListView.builder(
                      itemCount: (data['result'] as List).length,
                      itemBuilder: (context, index) {
                        final item = (data['result'] as List)[index];
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
                                      fontWeight: FontWeight.bold,
                                    ),
                                  ),
                                  subtitle: Text("Reason: ${item['reason']}"),
                                ),
                              ),
                              SizedBox(
                                child: item['type'] == "Absen masuk"
                                    ? TextButton(
                                        child: Text("Presensi masuk"),
                                        onPressed: () {
                                          if (item['status'] == "1") {
                                            if (!status.signedIn) {
                                              if (DateTime.now().isBefore(
                                                makeLimit(
                                                  schedule.start.split(':'),
                                                  config.tolerance +
                                                      config.ciLimit,
                                                ),
                                              )) {
                                                if (DateTime.now().isAfter(
                                                  makeLimit(
                                                    schedule.start.split(':'),
                                                    0,
                                                  ).subtract(
                                                    Duration(minutes: 60),
                                                  ),
                                                )) {
                                                  Navigator.pushNamed(
                                                    context,
                                                    '/signin',
                                                    arguments: {'ffocia': true},
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
                                                        seconds: 2,
                                                      ), // lama tampil
                                                      backgroundColor: Colors
                                                          .blue, // warna background
                                                    ),
                                                  );
                                                }
                                              } else {
                                                showDialog<bool>(
                                                  context: context,
                                                  builder: (context) => AlertDialog(
                                                    title: const Text(
                                                      'Kamu sudah tidak bisa absen masuk',
                                                    ),
                                                    content: const Text(
                                                      'Apakah kamu telah mengajukan pengecualian keterlambatan?',
                                                    ),
                                                    actions: [
                                                      TextButton(
                                                        onPressed: () => {
                                                          Navigator.pop(
                                                            context,
                                                            false,
                                                          ),
                                                        },
                                                        child: const Text(
                                                          'Belum',
                                                        ),
                                                      ),
                                                      TextButton(
                                                        onPressed: () => {
                                                          Navigator.pushNamed(
                                                            context,
                                                            '/signin',
                                                            arguments: {
                                                              'ffocia': true,
                                                            },
                                                          ),
                                                        },
                                                        child: const Text(
                                                          'Sudah',
                                                        ),
                                                      ),
                                                    ],
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
                                                  duration: Duration(
                                                    seconds: 2,
                                                  ), // lama tampil
                                                  backgroundColor: Colors
                                                      .blue, // warna background
                                                ),
                                              );
                                            }
                                          } else {
                                            ScaffoldMessenger.of(
                                              context,
                                            ).showSnackBar(
                                              SnackBar(
                                                content: Text(
                                                  'this exception is not approved yet',
                                                ),
                                                backgroundColor: Colors.blue,
                                                duration: Duration(seconds: 2),
                                              ),
                                            );
                                          }
                                        },
                                      )
                                    : SizedBox(),
                              ),
                              SizedBox(
                                child: item['type'] == "Lainnya"
                                    ? TextButton(
                                        child: Text("Presensi masuk"),
                                        onPressed: () {
                                          if (item['status'] == "1") {
                                            if (!status.signedIn) {
                                              if (DateTime.now().isBefore(
                                                makeLimit(
                                                  schedule.start.split(':'),
                                                  config.tolerance +
                                                      config.ciLimit,
                                                ),
                                              )) {
                                                if (DateTime.now().isAfter(
                                                  makeLimit(
                                                    schedule.start.split(':'),
                                                    0,
                                                  ).subtract(
                                                    Duration(minutes: 60),
                                                  ),
                                                )) {
                                                  Navigator.pushNamed(
                                                    context,
                                                    '/signin',
                                                    arguments: {'ffocia': true},
                                                  );
                                                } else {
                                                  ScaffoldMessenger.of(
                                                    context,
                                                  ).showSnackBar(
                                                    const SnackBar(
                                                      content: Text(
                                                        "Tunggu beberapa saat lagi",
                                                      ),
                                                      duration: Duration(
                                                        seconds: 2,
                                                      ), // lama tampil
                                                      backgroundColor: Colors
                                                          .blue, // warna background
                                                    ),
                                                  );
                                                }
                                              } else {
                                                Navigator.pushNamed(
                                                  context,
                                                  '/signin',
                                                  arguments: {'ffocia': true},
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
                                                  duration: Duration(
                                                    seconds: 2,
                                                  ), // lama tampil
                                                  backgroundColor: Colors
                                                      .blue, // warna background
                                                ),
                                              );
                                            }
                                          } else {
                                            ScaffoldMessenger.of(
                                              context,
                                            ).showSnackBar(
                                              SnackBar(
                                                content: Text(
                                                  'this exception is not approved yet',
                                                ),
                                                backgroundColor: Colors.blue,
                                                duration: Duration(seconds: 2),
                                              ),
                                            );
                                          }
                                        },
                                      )
                                    : SizedBox(),
                              ),

                              SizedBox(
                                child: item['type'] == "Cuti pulang"
                                    ? TextButton(
                                        child: Text("Presensi setengah hari"),
                                        onPressed: () {
                                          if (item['status'] == "1") {
                                            Navigator.pushNamed(
                                              context,
                                              '/signout',
                                              arguments: {'csh': true},
                                            );
                                          } else {
                                            SnackBar(
                                              content: Text(
                                                "Can not do presence out now",
                                              ),
                                              duration: Duration(
                                                seconds: 2,
                                              ), // lama tampil
                                              backgroundColor: Colors
                                                  .blue, // warna background
                                            );
                                          }
                                        },
                                      )
                                    : SizedBox(),
                              ),
                              SizedBox(
                                child: item['type'] == "Absen pulang"
                                    ? TextButton(
                                        child: Text("Presensi pulang"),
                                        onPressed: () {
                                          if (DateTime.now().isBefore(
                                            makeLimit(
                                              schedule.finish.split(':'),
                                              config.coLimit,
                                            ),
                                          )) {
                                            if (DateTime.now().isAfter(
                                              makeLimit(
                                                schedule.finish.split(':'),
                                                0,
                                              ),
                                            )) {
                                              if (item["status"] == "1") {
                                                ref
                                                    .read(
                                                      globalStateProvider
                                                          .notifier,
                                                    )
                                                    .fFOCOMakeAllowed();
                                                Navigator.pushNamed(
                                                  context,
                                                  '/signout',
                                                  arguments: {'csh': false},
                                                );
                                              } else {
                                                ScaffoldMessenger.of(
                                                  context,
                                                ).showSnackBar(
                                                  const SnackBar(
                                                    content: Text(
                                                      "this exception is not approved yet",
                                                    ),
                                                    duration: Duration(
                                                      seconds: 2,
                                                    ), // lama tampil
                                                    backgroundColor: Colors
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
                                                    "Can not do presence out now",
                                                  ),
                                                  duration: Duration(
                                                    seconds: 2,
                                                  ), // lama tampil
                                                  backgroundColor: Colors
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
                                                  "Kamu sudah tidak bisa absen pulang",
                                                ),
                                                duration: Duration(
                                                  seconds: 2,
                                                ), // lama tampil
                                                backgroundColor: Colors
                                                    .blue, // warna background
                                              ),
                                            );
                                          }
                                        },
                                      )
                                    : SizedBox(),
                              ),
                              TextButton(
                                child: Text("Edit"),
                                onPressed: () {
                                  Navigator.pushNamed(
                                    context,
                                    '/exception_edit',
                                    arguments: {
                                      'id': item['id'],
                                      'date': item['date'],
                                      'reason': item['reason'],
                                      'status': item['status'],
                                      'type': item['type'],
                                      'image': item['image'],
                                    },
                                  );
                                },
                              ),
                            ],
                          ),
                        );
                      },
                    );
                  }
                }

                return SizedBox();
              },
            )
          : SizedBox(),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.pushNamed(context, '/makeexception');
        },
        tooltip: 'Tambah',
        child: const Icon(Icons.add),
      ),
    );
  }
}
