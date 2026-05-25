import 'dart:convert';
import 'package:intl/intl.dart';
import 'package:timeago/timeago.dart' as timeago;
import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;

class JadwalCard extends StatelessWidget {
  final String hari;
  final String tanggal;
  final String jam;
  final String shift;
  final Color warnaTepi;

  const JadwalCard({
    super.key,
    required this.hari,
    required this.tanggal,
    required this.jam,
    required this.shift,
    this.warnaTepi = Colors.teal,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border.all(color: warnaTepi, width: 1),
        borderRadius: BorderRadius.circular(12),
        boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 4)],
      ),
      child: Row(
        children: [
          Column(
            children: [
              Text(hari, style: TextStyle(color: warnaTepi)),
              Text(
                tanggal,
                style: TextStyle(color: warnaTepi, fontWeight: FontWeight.bold),
              ),
            ],
          ),
          const SizedBox(width: 16),
          const VerticalDivider(),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  jam,
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
                ),
                Text(shift, style: const TextStyle(color: Colors.grey)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class SchedulePage extends ConsumerStatefulWidget {
  const SchedulePage({super.key});

  @override
  ConsumerState<SchedulePage> createState() => _SchedulePageState();
}

class _SchedulePageState extends ConsumerState<SchedulePage> {
  Future<http.Response>? list;

  Future<http.Response> getActivityList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    final now = DateTime.now();
    final date = DateFormat('yyyy-MM-dd').format(now);
    Uri url = Uri.parse(
      "${Env.api}/api/mobile/schedule/${other.pegawaiId}/$date",
    );

    return http.get(url);
  }

  Future<void> fetch() async {
    setState(() {
      list = null;
    });
    setState(() {
      list = getActivityList();
    });
  }

  @override
  void initState() {
    super.initState();
    fetch();
  }

  String isTodayFree(bool free, String start, String finish) {
    return free ? "Free" : "$start - $finish";
  }

  @override
  Widget build(BuildContext context) {
    final globalState = ref.read(globalStateProvider);
    final scheduleState = globalState.schedule;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Jadwal'),
        elevation: 0,
        backgroundColor: Colors.white,
        foregroundColor: Colors.black87,
      ),
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
                    final schedule = data['schedule'];
                    return ListView(
                      padding: EdgeInsets.all(16),
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                            vertical: 12,
                            horizontal: 16,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            boxShadow: [
                              BoxShadow(color: Colors.black12, blurRadius: 3),
                            ],
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(Icons.calendar_today_outlined, size: 16),
                              SizedBox(width: 8),
                              Text('${data['startDate']} - ${data['endDate']}'),
                            ],
                          ),
                        ),

                        const SizedBox(height: 16),

                        JadwalCard(
                          hari: data['schedule'][0]['dayName'],
                          tanggal: data['schedule'][0]['date'],
                          jam: isTodayFree(
                            schedule[0]['free'],
                            schedule[0]['start'],
                            schedule[0]['finish'],
                          ),
                          shift: scheduleState.workSystemName,
                        ),
                        JadwalCard(
                          hari: data['schedule'][1]['dayName'],
                          tanggal: data['schedule'][1]['date'],
                          jam: isTodayFree(
                            schedule[1]['free'],
                            schedule[1]['start'],
                            schedule[1]['finish'],
                          ),
                          shift: scheduleState.workSystemName,
                        ),
                        JadwalCard(
                          hari: data['schedule'][2]['dayName'],
                          tanggal: data['schedule'][2]['date'],
                          jam: isTodayFree(
                            schedule[2]['free'],
                            schedule[2]['start'],
                            schedule[2]['finish'],
                          ),
                          shift: scheduleState.workSystemName,
                        ),
                        JadwalCard(
                          hari: data['schedule'][3]['dayName'],
                          tanggal: data['schedule'][3]['date'],
                          jam: isTodayFree(
                            schedule[3]['free'],
                            schedule[3]['start'],
                            schedule[3]['finish'],
                          ),
                          shift: scheduleState.workSystemName,
                        ),
                        JadwalCard(
                          hari: data['schedule'][4]['dayName'],
                          tanggal: data['schedule'][4]['date'],
                          jam: isTodayFree(
                            schedule[4]['free'],
                            schedule[4]['start'],
                            schedule[4]['finish'],
                          ),
                          shift: scheduleState.workSystemName,
                        ),
                        JadwalCard(
                          hari: data['schedule'][5]['dayName'],
                          tanggal: data['schedule'][5]['date'],
                          jam: isTodayFree(
                            schedule[5]['free'],
                            schedule[5]['start'],
                            schedule[5]['finish'],
                          ),
                          shift: scheduleState.workSystemName,
                        ),
                        JadwalCard(
                          hari: data['schedule'][6]['dayName'],
                          tanggal: data['schedule'][6]['date'],
                          jam: isTodayFree(
                            schedule[6]['free'],
                            schedule[6]['start'],
                            schedule[6]['finish'],
                          ),
                          shift: scheduleState.workSystemName,
                        ),
                      ],
                    );
                  }
                }
              },
            )
          : SizedBox(),
    );
  }
}
