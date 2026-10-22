import 'dart:convert';
import 'package:camera/camera.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/global_state.dart';
import '../providers/location_provider.dart';
import '../env/env.dart';
import 'dart:async';

class TaskPage extends ConsumerStatefulWidget {
  final Future<Map<String, double>> coord;
  final CameraDescription camera;

  const TaskPage({super.key, required this.coord, required this.camera});

  @override
  ConsumerState<TaskPage> createState() => _TaskPageState();
}

class _TaskPageState extends ConsumerState<TaskPage> {
  late Future<http.Response>? list;
  DateTime? selectedDate;
  bool doneBtnIsClicked = false;

  Future<http.Response> getTaskList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse("${Env.api}/api/mobile/taskList/${other.pegawaiId}");

    var response = http.get(url);

    try {
      await response;
    } catch (e) {
      print(e);
    }

    return response;
  }

  Future<void> pickDate(BuildContext context) async {
    final DateTime now = DateTime.now();
    final DateTime yesterday = now.subtract(Duration(days: 1));

    final DateTime? date = await showDatePicker(
      context: context,
      initialDate: yesterday, // BUKAN hari ini!
      firstDate: DateTime(2000),
      lastDate: yesterday, // batas sampai kemarin
    );

    if (date != null) {
      selectedDate = date;
    }
  }

  Future<void> fetch() async {
    setState(() {
      list = null;
    });
    setState(() {
      list = getTaskList();
    });
  }

  @override
  void initState() {
    super.initState();
    fetch();
  }

  @override
  Widget build(BuildContext context) {
    final task = ref.read(globalStateProvider).task;
    return Scaffold(
      appBar: AppBar(title: Text("Penugasan")),
      body: list != null
          ? Column(
              children: [
                Padding(
                  padding: EdgeInsetsGeometry.all(12),
                  child: Row(
                    children: [
                      Expanded(
                        child: ElevatedButton(
                          onPressed: () => pickDate(context),
                          child: Text('Select date'),
                        ),
                      ),
                      SizedBox(width: 6),
                      ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: doneBtnIsClicked ? Colors.red : null,
                        ),
                        onPressed: () {
                          setState(() {
                            doneBtnIsClicked = !doneBtnIsClicked;
                          });
                        },
                        child: Text('Done'),
                      ),
                      SizedBox(width: 6),
                      ElevatedButton.icon(
                        onPressed: () {
                          if (doneBtnIsClicked) {
                            Navigator.pushNamed(
                              context,
                              '/done_task',
                              arguments: {'f': selectedDate},
                            );
                          } else {
                            if (selectedDate != null) {
                              Navigator.pushNamed(
                                context,
                                '/task_filter',
                                arguments: {'f': selectedDate},
                              );
                            } else {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text("please select date first"),
                                  duration: Duration(seconds: 2), // lama tampil
                                  backgroundColor:
                                      Colors.blue, // warna background
                                ),
                              );
                            }
                          }
                        },
                        label: Text('filter'),
                        icon: Icon(Icons.arrow_right),
                      ),
                    ],
                  ),
                ),
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
                                color: task.finished.contains(item['task_id']) ? Colors.green : Colors.white,
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
                                      child: ListTile(
                                        leading: Icon(Icons.event_note),
                                        title: Text(
                                          item['date'],
                                          style: TextStyle(
                                            fontWeight: FontWeight.bold,
                                            color:task.finished.contains(item['task_id']) ? Colors.white : Colors.black
                                          ),
                                        ),
                                        subtitle: Text(
                                          item['description'],
                                          style: TextStyle(
                                            fontWeight: FontWeight.bold,
                                            color:task.finished.contains(item['task_id']) ? Colors.white : Colors.black
                                          ),
                                        ),
                                      ),
                                    ),
                                    !task.started.contains(item['task_id'])
                                    ?
                                    IconButton(
                                      icon: const Icon(Icons.login),
                                      onPressed: () {
                                        final submittedAt = DateTime.parse(item['created_at']);
                                        final deadline = submittedAt.add(const Duration(hours: 6));

                                        if(DateTime.now().isAfter(deadline)) {
                                          ScaffoldMessenger.of(
                                            context
                                          )
                                          .showSnackBar(
                                            SnackBar(
                                              content: Text(
                                                'Form tugas ini sudah kadaluwarsa',
                                              ),
                                              backgroundColor: Colors.blue,
                                              duration: Duration(seconds: 2),
                                            ),
                                          );
                                        } 
                                        else {
                                          ref.refresh(locationProvider);
                                          Navigator.pushNamed(
                                            context,
                                            '/task_start',
                                            arguments: {
                                              'task_id': item['task_id'],
                                            },
                                          );
                                        }
                                      }
                                    )
                                    :
                                    SizedBox(),
                                    !task.finished.contains(item['task_id']) 
                                    ?
                                    IconButton(
                                      icon: const Icon(Icons.logout),
                                      onPressed: () {
                                        
                                        final submittedAt = DateTime.parse(item['created_at']);
                                        final deadline = submittedAt.add(const Duration(hours: 6));
                                      

                                        if(DateTime.now().isAfter(deadline)) {
                                          ScaffoldMessenger.of(
                                            context,
                                          )
                                          .showSnackBar(
                                            SnackBar(
                                              content: Text(
                                                'Form tugas ini sudah kadaluwarsa',
                                              ),
                                              backgroundColor: Colors.blue,
                                              duration: Duration(seconds: 2),
                                            ),
                                          );
                                        } 
                                        else {
                                          ref.refresh(locationProvider);
                                          Navigator.pushNamed(
                                            context,
                                            '/task_end',
                                            arguments: {
                                              'task_id': item['task_id'],
                                            },
                                          );
                                        }
                                      },
                                    )
                                    :
                                    SizedBox(),
                                    IconButton(
                                      color:task.finished.contains(item['task_id']) ? Colors.white : Colors.black,
                                      icon: const Icon(Icons.edit),
                                      onPressed: () {
                                        final limit = DateTime.parse(
                                          "${item['date']} 00:00:00",
                                        ).add(Duration(days: 1));
                                        if (DateTime.now().isAfter(limit)) {
                                          ScaffoldMessenger.of(
                                            context,
                                          ).showSnackBar(
                                            SnackBar(
                                              content: Text(
                                                'Form tugas ini sudah kadaluwarsa',
                                              ),
                                              backgroundColor: Colors.blue,
                                              duration: Duration(seconds: 2),
                                            ),
                                          );
                                        } else {
                                          Navigator.pushNamed(
                                            context,
                                            '/task_edit',
                                            arguments: {
                                              'date': item['date'],
                                              'desc': item['description'],
                                              'id': item['task_id'],
                                            },
                                          );
                                        }
                                      },
)
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
