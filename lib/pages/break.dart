import 'dart:convert';
import 'dart:math';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/global_state.dart';
import '../env/env.dart';
import 'dart:async';
import 'package:intl/intl.dart';

final breakEndProvider = StateProvider<DateTime>((ref) {
  return DateTime.now().add(Duration(minutes: 1)); // contoh default
});

class BreakPage extends ConsumerStatefulWidget {
  final Future<Map<String, double>> coord;

  const BreakPage({super.key, required this.coord});

  @override
  ConsumerState<BreakPage> createState() => _BreakPageState();
}

class _BreakPageState extends ConsumerState<BreakPage> {
  double latitude = 0;
  double longitude = 0;
  String locationName = 'Anda berada di luar area presensi';

  late Timer _timer;
  String _timeString = "";
  double _progress = 0;

  double toRad(double degree) {
    return degree * pi / 180;
  }

  num haversineDistance(lat1, lat2, lon1, lon2) {
    num dLat = toRad(lat2 - lat1);
    num dLon = toRad(lon2 - lon1);
    num cosinus1 = cos(toRad(lat1));
    num cosinus2 = cos(toRad(lat2));
    num cosValue = cosinus1 * cosinus2;
    num sinus1 = pow(sin(dLat / 2), 2);
    num sinus2 = pow(sin(dLon / 2), 2);
    num v = sinus1 + (cosValue * sinus2);

    double result = 6371 * (1000 * (2 * asin(sqrt(v))));

    return double.parse(result.toStringAsFixed(2));
  }

  @override
  void initState() {
    super.initState();

    DateTime start = DateTime.now();
    final globalState = ref.read(globalStateProvider);
    final afterBreakTime = globalState.schedule.breakFinish;
    final parts = afterBreakTime.split(":");

    DateTime time = DateTime(
      start.year,
      start.month,
      start.day,
      int.parse(parts[0]),
      int.parse(parts[1]),
      int.parse(parts[2]),
    );

    int diff = time.difference(start).inSeconds;

    double interval = (diff / 1000) * 1000;

    // timer tiap 1 detik
    _timer = Timer.periodic(Duration(milliseconds: interval.round()), (
      Timer t,
    ) {
      setState(() {
        _timeString = _formatDateTime(DateTime.now());
      });

      setState(() {
        _progress = _progress + 0.001;
      });
    });

    WidgetsBinding.instance.addPostFrameCallback((_) async {
      await Future.delayed(Duration(seconds: 2));
      final globalState = ref.read(globalStateProvider);
      final locations = globalState.location.list;
      final coordinate = await widget.coord;

      for (var loc in locations) {
        final lat1 = coordinate['lat'];
        final lat2 = double.parse(loc['lat']);
        final lon1 = coordinate['lon'];
        final lon2 = double.parse(loc['lon']);

        if (haversineDistance(lat1, lat2, lon1, lon2) < 200) {
          setState(() {
            latitude = lat2;
            longitude = lon2;
            locationName = loc['locationName'];
          });
        } else {
          setState(() {
            latitude = lat1 as double;
            longitude = lon1 as double;
          });
        }
      }
    });
  }

  void breakStart() async {
    final headers = {'Content-Type': 'application/json'};
    final globalState = ref.read(globalStateProvider);
    final url = Uri.parse("${Env.api}/api/mobile/break");
    final now = DateFormat('HH:mm').format(DateTime.now());
    final employeeId = globalState.other.pegawaiId;

    final params = {'jam_istirahat': now, 'pegawai_id': employeeId};

    try {
      await http.post(url, headers: headers, body: jsonEncode(params));

      ref.read(globalStateProvider.notifier).breakStart();

      Navigator.pushNamedAndRemoveUntil(
        context,
        '/',
        (Route<dynamic> route) => false,
      );
    } catch (e) {
      print(e);
    }
  }

  String _formatDateTime(DateTime dateTime) {
    return DateFormat('HH:mm').format(dateTime);
  }

  @override
  void dispose() {
    _timer.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final globalState = ref.read(globalStateProvider);
    final schedule = globalState.schedule;
    final workSystemName = schedule.workSystemName;

    return Scaffold(
      body: _timeString != ""
          ? SafeArea(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    "Mulai Istirahat",
                    style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500),
                  ),
                  SizedBox(height: 20),
                  Stack(
                    alignment: Alignment.center,
                    children: [
                      SizedBox(
                        height: 200,
                        width: 200,
                        child: CircularProgressIndicator(
                          value: _progress, // progress jalan otomatis
                          strokeWidth: 12,
                          valueColor: AlwaysStoppedAnimation<Color>(
                            Colors.green,
                          ),
                          backgroundColor: Colors.grey.shade200,
                        ),
                      ),
                      Text(
                        _timeString,
                        style: TextStyle(
                          fontSize: 32,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: 30),
                  Container(
                    padding: EdgeInsets.all(12),
                    margin: EdgeInsets.symmetric(horizontal: 20),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: Colors.grey.shade300),
                    ),
                    child: Column(
                      children: [
                        Row(
                          children: [
                            Icon(Icons.calendar_today, size: 18),
                            SizedBox(width: 8),
                            Text(
                              "$workSystemName - ${DateFormat('EEEE, dd MMM yyyy').format(DateTime.now())}",
                            ),
                          ],
                        ),
                        SizedBox(height: 8),
                        Row(
                          children: [
                            Icon(
                              Icons.location_on,
                              size: 18,
                              color: Colors.red,
                            ),
                            SizedBox(width: 8),
                            Text(locationName),
                          ],
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: EdgeInsets.all(12),
                    margin: EdgeInsets.symmetric(horizontal: 8),
                    child: SizedBox(
                      width: double.infinity, // membuat selebar layar
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green[600], // hijau gelap
                          padding: EdgeInsets.symmetric(
                            vertical: 16,
                          ), // tinggi button
                        ),
                        onPressed: () {
                          breakStart();
                        },
                        child: Text(
                          "Mulai",
                          style: TextStyle(
                            color: Colors.white, // warna teks putih
                            fontSize: 16,
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            )
          : Center(child: CircularProgressIndicator()),
    );
  }
}
