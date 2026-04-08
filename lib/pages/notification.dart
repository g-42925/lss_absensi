import 'dart:convert';
import 'package:timeago/timeago.dart' as timeago;
import 'package:absensiart';
import 'package:absensi/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;

class NotificationPage extends ConsumerStatefulWidget {
  const NotificationPage({super.key});

  @override
  ConsumerState<NotificationPage> createState() => _NotificationPageState();
}

class _NotificationPageState extends ConsumerState<NotificationPage> {
  late Future<http.Response>? list;

  Future<http.Response> getNotificationList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse(
      "${Env.api}/api/mobile/notificationList/${other.pegawaiId}",
    );

    return http.get(url);
  }

  Future<void> fetch() async {
    setState(() {
      list = null;
    });
    setState(() {
      list = getNotificationList();
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
      appBar: AppBar(title: const Text('Notifikasi'), centerTitle: true),
      body: list != null
          ? FutureBuilder(
              future: list as Future<http.Response>,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Center(child: CircularProgressIndicator());
                } else {
                  if (snapshot.hasError) {
                    return Center(
                      child: Text(
                        "something went wrong or no notification exist",
                      ),
                    );
                  } else {
                    final response = snapshot.data!;
                    final data = jsonDecode(response.body);
                    return ListView.builder(
                      itemCount: data.length,
                      itemBuilder: (context, index) {
                        final notif = data[index];
                        return Card(
                          elevation: notif['seen'] == 1 ? 0 : 2,
                          margin: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 6,
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          color: notif['seen'] == 0
                              ? Colors.grey[200]
                              : Colors.white,
                          child: ListTile(
                            leading: CircleAvatar(
                              backgroundColor: notif['seen'] == 1
                                  ? Colors.grey
                                  : Colors.blueAccent,
                              child: const Icon(
                                Icons.notifications,
                                color: Colors.white,
                              ),
                            ),
                            title: Text(
                              "New Notification",
                              style: TextStyle(
                                fontWeight: notif['seen'] == 1
                                    ? FontWeight.normal
                                    : FontWeight.bold,
                              ),
                            ),
                            subtitle: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(notif['description']),
                                const SizedBox(height: 4),
                                Text(
                                  timeago.format(
                                    DateTime.parse(notif['date']),
                                    locale: 'en',
                                  ),
                                  style: const TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    );
                  }
                }
              },
            )
          : SizedBox(),
    );
  }
}
