import 'dart:convert';

import 'package:absensi/env/env.dart';
import 'package:intl/intl.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;
import 'package:table_calendar/table_calendar.dart';

class CalendarPage extends ConsumerStatefulWidget {
  const CalendarPage({super.key});

  @override
  ConsumerState<CalendarPage> createState() => _CalendarPageState();
}

class _CalendarPageState extends ConsumerState<CalendarPage> {
  CalendarFormat _calendarFormat = CalendarFormat.month;
  DateTime _focusedDay = DateTime.now();
  DateTime? _selectedDay;

  Future<List<String>>? _futureEvents;
  late Future<http.Response>? list;

  // simulasi fetch data (misal dari API)
  Future<List<String>> fetchEvents(DateTime date) async {
    await Future.delayed(const Duration(milliseconds: 800));
    return [
      "Event A pada ${date.toLocal()}",
      "Event B pada ${date.toLocal()}",
      "Event C pada ${date.toLocal()}",
    ];
  }

  Future<http.Response> getEventList(String empId, String date) {
    Uri url = Uri.parse("${Env.api}/api/mobile/calendar/$empId/$date");

    return http.get(url);
  }

  Future<void> fetch(String empId, String date) async {
    setState(() {
      list = null;
    });
    setState(() {
      list = getEventList(empId, date);
    });
  }

  @override
  void initState() {
    super.initState();
    final globalState = ref.read(globalStateProvider);
    final employeeId = globalState.other.pegawaiId;
    fetch(employeeId, '1970-01-01');
  }

  @override
  Widget build(BuildContext context) {
    final globalState = ref.read(globalStateProvider);
    final employeeId = globalState.other.pegawaiId;

    return Scaffold(
      appBar: AppBar(title: const Text("Kalender")),
      body: Column(
        children: [
          TableCalendar(
            firstDay: DateTime.utc(2020, 1, 1),
            lastDay: DateTime.utc(2030, 12, 31),
            focusedDay: _focusedDay,
            selectedDayPredicate: (day) => isSameDay(_selectedDay, day),
            onDaySelected: (selectedDay, focusedDay) {
              setState(() {
                _selectedDay = selectedDay;
                _focusedDay = focusedDay;
              });

              fetch(
                employeeId,
                DateFormat('yyyy-MM-dd').format(_selectedDay!),
              ); // fetch saat klik
            },
            calendarFormat: _calendarFormat,
            onFormatChanged: (format) {
              setState(() {
                _calendarFormat = format;
              });
            },
          ),
          const Divider(),
          Expanded(
            child: list != null
                ? FutureBuilder(
                    future: list,
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) {
                        return const Center(child: CircularProgressIndicator());
                      } else if (snapshot.hasError) {
                        return Center(child: Text("Error: ${snapshot.error}"));
                      } else {
                        final response = snapshot.data!;
                        print(response.body);
                        final data = jsonDecode(response.body);
                        final permission = List<Map<String, dynamic>>.from(
                          data['permission'],
                        );
                        final globalHolidays = List<Map<String, dynamic>>.from(
                          data['globalHolidays'],
                        );
                        final companyHolidays = List<Map<String, dynamic>>.from(
                          data['companyHolidays'],
                        );

                        return ListView(
                          children: [
                            ListView.builder(
                              itemCount: permission.length,
                              shrinkWrap: true,
                              physics: NeverScrollableScrollPhysics(),
                              itemBuilder: (context, index) {
                                final target = permission[index];
                                return ListTile(
                                  title: Text(
                                    "${target['catatan_awal']} (${target['tanggal_request']} - ${target['tanggal_request_end']})",
                                  ),
                                );
                              },
                            ),
                            ListView.builder(
                              itemCount: globalHolidays.length,
                              shrinkWrap: true,
                              physics: NeverScrollableScrollPhysics(),
                              itemBuilder: (context, index) {
                                final target = globalHolidays[index];
                                return ListTile(
                                  title: Text(
                                    "${target['keterangan']} (${target['tanggal']} - ${target['sampai_tanggal']})",
                                  ),
                                );
                              },
                            ),
                            ListView.builder(
                              itemCount: companyHolidays.length,
                              shrinkWrap: true,
                              physics: NeverScrollableScrollPhysics(),
                              itemBuilder: (context, index) {
                                final target = companyHolidays[index];
                                return ListTile(
                                  title: Text(
                                    "${target['keterangan']} (${target['tanggal']} - ${target['sampai_tanggal']})",
                                  ),
                                );
                              },
                            ),
                          ],
                        );
                      }
                      return SizedBox();
                    },
                  )
                : const Center(
                    child: Text("Pilih tanggal untuk melihat event"),
                  ),
          ),
        ],
      ),
    );
  }

  // Widget build(BuildContext context) {
  // final globalState = ref.read(globalStateProvider);
  // final employeeId = globalState.other.pegawaiId;

  //   return Scaffold(
  //     appBar: AppBar(title: const Text("Kalender Flutter")),
  //     body: Column(
  //       children: [
  //         TableCalendar(
  //           firstDay: DateTime.utc(2020, 1, 1),
  //           lastDay: DateTime.utc(2030, 12, 31),
  //           focusedDay: _focusedDay,
  //           selectedDayPredicate: (day) => isSameDay(_selectedDay, day),
  //           onDaySelected: (selectedDay, focusedDay) {
  //             setState(() {
  //               _selectedDay = selectedDay;
  //               _focusedDay = focusedDay;
  //             });

  //             fetch(employeeId); // fetch saat klik
  //           },
  //           calendarFormat: _calendarFormat,
  //           onFormatChanged: (format) {
  //             setState(() {
  //               _calendarFormat = format;
  //             });
  //           },
  //         ),
  //         const Divider(),
  //         Expanded(
  //           child: list != null
  //               ? FutureBuilder<List<String>>(
  //                   future: _futureEvents,
  //                   builder: (context, snapshot) {
  //                     if (snapshot.connectionState == ConnectionState.waiting) {
  //                       return const Center(child: CircularProgressIndicator());
  //                     } else if (snapshot.hasError) {
  //                       return Center(child: Text("Error: ${snapshot.error}"));
  //                     } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
  //                       return const Center(child: Text("Tidak ada event"));
  //                     } else {
  //                       final events = snapshot.data!;
  //                       print(events);
  //                     }
  //                     return SizedBox();
  //                   },
  //                 )
  //               : const Center(
  //                   child: Text("Pilih tanggal untuk melihat event"),
  //                 ),
  //         ),
  //       ],
  //     ),
  //   );
  // }
}
