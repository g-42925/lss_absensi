import 'dart:convert';
import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;

class ActivityPage extends ConsumerStatefulWidget {
  const ActivityPage({super.key});

  @override
  ConsumerState<ActivityPage> createState() => _ActivityPageState();
}

class _ActivityPageState extends ConsumerState<ActivityPage> {
  Future<http.Response>? list;

  Future<http.Response> getActivityList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse(
      "${Env.api}/api/mobile/activityList/${other.pegawaiId}",
    );

    return http.get(url);
  }

  Future<void> fetch() async {
    setState(() {
      list = getActivityList();
    });
  }

  @override
  void initState() {
    super.initState();
    fetch();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Aktivitas'), centerTitle: true),
      body: list != null
          ? FutureBuilder(
              future: list,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Center(child: CircularProgressIndicator());
                }
                if (snapshot.hasError) {
                  return Center(
                    child: Text("something went wrong or no activity exist"),
                  );
                } else {
                  final response = snapshot.data!;
                  //final data = jsonDecode(response.body);
                  final body = response.body;

                  dynamic data;
                  try {
                    data = jsonDecode(body);
                  } catch (e) {
                    return Center(child: Text("Invalid server response"));
                  }

                  if (data is! List) {
                    return Center(child: Text("No activity available"));
                  }
                  if (response.statusCode != 200) {
                    return Center(
                      child: Text("Server error: ${response.statusCode}"),
                    );
                  }

                  return ListView.builder(
                    itemCount: data.length,
                    itemBuilder: (context, index) {
                      final notif = data[index];
                      if (notif['type'] == "Check In") {
                        return Card(
                          margin: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 6,
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: ListTile(
                            leading: CircleAvatar(
                              child: Icon(
                                Icons.notifications,
                                color: notif['late'] as bool
                                    ? Colors.red
                                    : Colors.white,
                              ),
                            ),
                            title: Text(notif['type'], style: TextStyle()),
                            subtitle: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  "Kamu telah melakukan absen masuk pada pukul ${notif['time']}",
                                ),
                                const SizedBox(height: 4),
                              ],
                            ),
                          ),
                        );
                      }

                      if (notif['type'] == "Check Out") {
                        return Card(
                          margin: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 6,
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: ListTile(
                            leading: CircleAvatar(
                              child: Icon(
                                Icons.notifications,
                                color: notif['late'] as bool
                                    ? Colors.red
                                    : Colors.white,
                              ),
                            ),
                            title: Text(notif['type'], style: TextStyle()),
                            subtitle: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  "Kamu telah melakukan absen keluar pada pukul ${notif['time']}",
                                ),
                                const SizedBox(height: 4),
                              ],
                            ),
                          ),
                        );
                      }

                      if (notif['type'] == 'Assignment') {
                        return Card(
                          margin: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 6,
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: ListTile(
                            leading: CircleAvatar(
                              child: Icon(
                                Icons.notifications,
                                color: Colors.white,
                              ),
                            ),
                            title: Text(notif['type'], style: TextStyle()),
                            subtitle: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  "Kamu telah menyelesaikan tugas pada ${notif['date']} ${notif['time']}",
                                ),
                                const SizedBox(height: 4),
                              ],
                            ),
                          ),
                        );
                      }

                      return SizedBox();
                    },
                  );
                }
              },
            )
          : SizedBox(),
    );
  }
}
