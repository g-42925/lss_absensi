import 'dart:convert';
import 'package:camera/camera.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../providers/global_state.dart';
import '../env/env.dart';
import 'dart:async';

class TaskFilterPage extends ConsumerStatefulWidget {
  final Future<Map<String, double>> coord;
  final CameraDescription camera;

  const TaskFilterPage({super.key, required this.coord, required this.camera});

  @override
  ConsumerState<TaskFilterPage> createState() => _TaskPageState();
}

class _TaskPageState extends ConsumerState<TaskFilterPage> {
  late Future<http.Response>? list;
  DateTime? selectedDate;

  Future<http.Response> getTaskList(DateTime filter) async {
    print(filter);
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    final f = DateFormat('yyyy-MM-dd').format(filter);
    Uri url = Uri.parse(
      "${Env.api}/api/mobile/filteredTaskList/${other.pegawaiId}/$f",
    );

    var response = http.get(url);

    try {
      await response;
    } catch (e) {
      print(e);
    }

    return response;
  }

  Future<void> pickDate(BuildContext context) async {
    final DateTime? date = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2000),
      lastDate: DateTime(2100),
    );

    if (date != null) {
      selectedDate = date;
    }
  }

  Future<void> fetch(Map<String, dynamic> args) async {
    setState(() {
      list = null;
    });
    setState(() {
      list = getTaskList(args['f']);
    });
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    final args = ModalRoute.of(context)!.settings.arguments;
    fetch(args as Map<String, dynamic>);
  }

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    final task = ref.read(globalStateProvider).task;
    final args =
        ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;
    return Scaffold(
      appBar: AppBar(title: Text("Penugasan")),
      body: list != null
          ? Column(
              children: [
                Expanded(
                  child: FutureBuilder(
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
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(
                                    6,
                                  ), // ubah angka 20 sesuai keinginan
                                ),
                                margin: EdgeInsets.symmetric(
                                  horizontal: 12,
                                  vertical: 6,
                                ),
                                elevation: 3,
                                child: Row(
                                  children: [
                                    Expanded(
                                      child: Center(
                                        child: ListTile(
                                          leading: Icon(Icons.event_note),
                                          title: Text(
                                            "Tgl: ${item['date']}",
                                            style: TextStyle(
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                          subtitle: Text(
                                            "Description: ${item['description']}",
                                          ),
                                        ),
                                      ),
                                    ),
                                    Column(
                                      children: [
                                        IconButton(
                                          icon: Icon(Icons.image), // ikon foto
                                          onPressed: () {
                                            showGeneralDialog(
                                              context: context,
                                              barrierDismissible: true,
                                              barrierLabel: "Lightbox",
                                              barrierColor: Colors.black
                                                  .withOpacity(0.7),
                                              transitionDuration:
                                                  const Duration(
                                                    milliseconds: 300,
                                                  ),
                                              pageBuilder: (_, __, ___) {
                                                return Center(
                                                  child: InteractiveViewer(
                                                    child: Image.network(
                                                      item['start_photo'],
                                                    ),
                                                  ),
                                                );
                                              },
                                              transitionBuilder:
                                                  (_, anim, __, child) {
                                                    return ScaleTransition(
                                                      scale: CurvedAnimation(
                                                        parent: anim,
                                                        curve: Curves.easeOut,
                                                      ),
                                                      child: child,
                                                    );
                                                  },
                                            );
                                          },
                                        ),
                                        Text("Memulai"),
                                      ],
                                    ),
                                    SizedBox(width: 6),
                                    Column(
                                      children: [
                                        IconButton(
                                          icon: Icon(Icons.image), // ikon foto
                                          onPressed: () {
                                            showGeneralDialog(
                                              context: context,
                                              barrierDismissible: true,
                                              barrierLabel: "Lightbox",
                                              barrierColor: Colors.black
                                                  .withOpacity(0.7),
                                              transitionDuration:
                                                  const Duration(
                                                    milliseconds: 300,
                                                  ),
                                              pageBuilder: (_, __, ___) {
                                                return Center(
                                                  child: InteractiveViewer(
                                                    child: Image.network(
                                                      item['finish_photo'],
                                                    ),
                                                  ),
                                                );
                                              },
                                              transitionBuilder:
                                                  (_, anim, __, child) {
                                                    return ScaleTransition(
                                                      scale: CurvedAnimation(
                                                        parent: anim,
                                                        curve: Curves.easeOut,
                                                      ),
                                                      child: child,
                                                    );
                                                  },
                                            );
                                          },
                                        ),
                                        Text("Selesai"),
                                      ],
                                    ),
                                    SizedBox(width: 6),
                                  ],
                                ),
                              );
                            },
                          );
                        }
                      }
                      return SizedBox();
                    },
                  ),
                ),
              ],
            )
          : SizedBox(),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.pushNamed(context, '/make_task');
        },
        tooltip: 'Tambah',
        child: const Icon(Icons.add),
      ),
    );
  }
}
