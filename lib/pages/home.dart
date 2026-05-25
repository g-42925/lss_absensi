import 'dart:async';

import 'package:absensi;
import 'package:intl/intl.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import '../providers/global_state.dart';
import '../providers/location_provider.dart';

class MyHomePage extends ConsumerStatefulWidget {
  const MyHomePage({super.key});

  @override
  ConsumerState<MyHomePage> createState() => _MyHomePageState();
}

class _MyHomePageState extends ConsumerState<MyHomePage> {
  bool stillOnTheSameDay = false;
  late Future<bool>? loggedIn;
  final supabase = Supabase.instance.client;

  Future<void> isLoggedIn(bool loggedIn) async {
    setState(() {
      loggedIn = loggedIn;
    });
  }

  @override
  void initState() {
    super.initState();
  }

  DateTime makeTime(DateTime param) {
    DateTime now = DateTime.now();

    return DateTime(
      now.year,
      now.month,
      now.day,
      param.hour,
      param.minute,
    );
  }

  Widget SplashScreen(loggedIn) {
    Future.delayed(const Duration(seconds: 8), () {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const LoginPage()),
      );
    });

    return Center(child: CircularProgressIndicator());
  }

  int _selectedIndex = 0;

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });

    if (index == 3) {
      Navigator.pushNamed(context, '/notification');
    }

    if (index == 2) {
      Navigator.pushNamed(context, '/activity');
    }

    if (index == 1) {
      Navigator.pushNamed(context, '/schedule');
    }
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
    final globalState = ref.read(globalStateProvider);
    final holiday = globalState.holiday;
    final company = globalState.company;
    final schedule = globalState.schedule;
    final status = globalState.status;
    final auth = globalState.auth;
    final config = globalState.config;
    final breakInfo = globalState.breakInfo;
    final presence = globalState.presence;
    final breakStartTime = auth.loggedIn ? schedule.breakStart : "00:00";
    final breakStart = DateFormat("HH:mm").parse(breakStartTime);
    final pp = globalState.other.fotoPegawai;

    final String message = "Fokus pada langkah, bukan jaraknya";
    final ciLable = presence.ci == "00:00" ? "Check-In" : "${presence.ci}";
    final coLable = presence.co == "00:00" ? "Check-Out" : "${presence.co}";

    return auth.loggedIn
        ? Scaffold(
            appBar: AppBar(
              automaticallyImplyLeading: false,
              title: Row(
                children: [
                  Image.asset('assets/logo.png', height: 50),
                  const SizedBox(width: 8),
                  const Text(
                    'Leryn Absensi',
                    style: TextStyle(color: Colors.black),
                  ),
                ],
              ),
  backgroundColor: Color(0xFFE5E7EB),              elevation: 0,
              actions: [
                Padding(
                  padding: EdgeInsets.all(8.0),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(24), // sudut melengkung
                    child: Image.network(
                      pp, // Ganti dengan path image kamu
                      height: 45,
                    ),
                  ),
                ),
              ],
            ),
            body: Container(
              height: double.infinity,
              decoration: BoxDecoration(
                gradient:LinearGradient(
  begin: Alignment.topLeft,
  end: Alignment.bottomRight,
  colors: [
    Color(0xFFF3F4F6), // gray-100
    Color(0xFFE5E7EB), // gray-200
  ],
),
                borderRadius: BorderRadius.circular(10),
              ),
              child: SingleChildScrollView(
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                            colors: [
                              Color(0xFF004D40), // hijau toska sangat gelap
                              Color(0xFF00796B), // hijau toska gelap
                              Color(0xFF26A69A), // toska terang
                            ],
                            stops: [0.0, 0.5, 1.0],
                          ),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Column(
                          children: [
                            Row(
                              children: [
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(
                                    16.0,
                                  ), // ubah angka untuk radius berbeda
                                  child: Image.network(
                                    "${company.logo}",
                                    width: 70,
                                    height: 70,
                                    fit: BoxFit.cover,
                                  ),
                                ),
                                SizedBox(width: 15),
                                SizedBox(
                                  width: 250,
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        company.name,
                                        style: TextStyle(
                                          color: Colors.white,
                                          fontWeight: FontWeight.bold,
                                          fontSize: 18,
                                        ),
                                      ),
                                      Text(
                                        message,
                                        overflow: TextOverflow.visible,
                                        style: TextStyle(color: Colors.white),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                            SizedBox(height: 16),
                            Container(
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(8),
                                color: Colors.white,
                              ),
                              width: double.infinity,
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.center,
                                children: holiday.holiday || !holiday.workDay
                                    ? [Text("Selamat Berlibur")]
                                    : [
                                        Text("Jadwal Anda Hari Ini"),
                                        SizedBox(height: 6),
                                        SizedBox(
                                          width:
                                              MediaQuery.of(
                                                context,
                                              ).size.width *
                                              0.5,
                                          child: Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.spaceBetween,
                                            children: [
                                              Row(
                                                children: [
                                                  Icon(Icons.login),
                                                  SizedBox(width: 4),
                                                  Text(schedule.start),
                                                ],
                                              ),
                                              Text('...'),
                                              Row(
                                                children: [
                                                  Icon(Icons.logout),
                                                  SizedBox(width: 4),
                                                  Text(schedule.finish),
                                                ],
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 20),
                      GridView.count(
                        crossAxisCount: 4,
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        children: [
                          IconLabel(
                            icon: Icons.login,
                            label: "Check-in",
                            onPressed: () {
                              if (!status.signedIn) {
                                if (DateTime.now().isBefore(
                                  makeLimit(
                                    schedule.start.split(':'),
                                    config.tolerance + config.ciLimit,
                                  ),
                                )) {
                                  if (DateTime.now().isAfter(
                                    makeLimit(
                                      schedule.start.split(':'),
                                      0,
                                    ).subtract(Duration(minutes: 60)),
                                  )) {
                                    ref.refresh(locationProvider);
                                    Navigator.pushNamed(
                                      context,
                                      '/signin',
                                      arguments: {'ffocia': false},
                                    );
                                  } 
                                  else {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                        content: Text(
                                          "belum bisa absen masuk sekarang",
                                        ),
                                        duration: Duration(
                                          seconds: 2,
                                        ), // lama tampil
                                        backgroundColor:
                                            Colors.blue, // warna background
                                      ),
                                    );
                                  }
                                } 
                                else {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                        "Kamu sudah tidak bisa absen masuk",
                                      ),
                                      duration: Duration(
                                        seconds: 2,
                                      ), // lama tampil
                                      backgroundColor:
                                          Colors.blue, // warna background
                                    ),
                                  );
                                }
                              } 
                              else {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text("Kamu sudah absen"),
                                    duration: Duration(
                                      seconds: 2,
                                    ), // lama tampil
                                    backgroundColor:
                                        Colors.blue, // warna background
                                  ),
                                );
                              }
                            },
                          ),
                          IconLabel(
                            icon: Icons.local_cafe,
                            label: 'Istirahat',
                            onPressed: () {
                              print(makeTime(breakStart));
                              if(!breakInfo.onBreak){
                                if (DateTime.now().isAfter(
                                  makeTime(breakStart),
                                )) {
                                  ref.refresh(locationProvider);
                                  Navigator.pushNamed(context, '/break');
                                } else {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text("Belum waktunya istirahat"),
                                      duration: Duration(
                                        seconds: 2,
                                      ), // lama tampil
                                      backgroundColor:
                                          Colors.blue, // warna background
                                    ),
                                  );
                                }
                              }
                              else{
                                ref.refresh(locationProvider);
                                Navigator.pushNamed(context, '/breakend');
                              }
                            },
                          ),
                          IconLabel(
                            icon: Icons.access_time,
                            label: 'Lembur',
                            onPressed: () {
                              Navigator.pushNamed(context, '/overwork');
                            },
                          ),
                          IconLabel(
                            icon: Icons.event_busy,
                            label: 'Cuti',
                            onPressed: () {
                              Navigator.pushNamed(context, '/leave');
                            },
                          ),
                          IconLabel(
                            icon: Icons.door_front_door,
                            label: 'Izin',
                            onPressed: () {
                              Navigator.pushNamed(context, '/permission');
                            },
                          ),
                          IconLabel(
                            icon: Icons.calendar_today,
                            label: 'Kalender',
                            onPressed: () {
                              Navigator.pushNamed(context, '/calendar');
                            },
                          ),
                          IconLabel(
                            icon: Icons.group,
                            label: 'Karyawan',
                            onPressed: () {
                              Navigator.pushNamed(context, '/employees');
                            },
                          ),
                          IconLabel(
                            onPressed: () {
                              Navigator.pushNamed(context, '/salary');
                            },
                            icon: Icons.account_balance_wallet,
                            label: 'Gaji',
                          ),
                          IconLabel(
                            onPressed: () {
                              Navigator.pushNamed(context, '/claim');
                            },
                            icon: Icons.assignment_turned_in,
                            label: 'Klaim',
                          ),
                          IconLabel(
                            icon: Icons.note_add,
                            label: 'Pengecualian',
                            onPressed: () {
                              Navigator.pushNamed(context, '/exception');
                            },
                          ),
                          IconLabel(
                            icon: Icons.work,
                            label: 'Tugas',
                            onPressed: () {
                              Navigator.pushNamed(
                                context,
                                '/task',
                                arguments: {'csh': false},
                              );
                            },
                          ),
                          IconLabel(
                            icon: Icons.logout,
                            label: "Check-out",
                            onPressed: () {
                              ref.refresh(locationProvider);
                              Navigator.pushNamed(
                                context, '/signout',
                                arguments: {'csh': false},
                              );
                            },
                          ),
                        ],
                      ),
                      const SizedBox(height: 0),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: const Color(0xFFD1FFDC), // Hijau muda
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: GestureDetector(
                          onTap:(){
                            Navigator.pushNamed(
                              context, '/log',
                              arguments: {'csh': false},
                            );                            
                          },
                          child: Row(
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: const [
                                    Text(
                                      "Ringkasan Kehadiran",
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.green,
                                      ),
                                    ),
                                    SizedBox(height: 4),
                                    Text(
                                      "Periksa kinerja rekap Anda bulan ini",
                                      style: TextStyle(
                                        fontSize: 12,
                                        color: Colors.black54,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              Image.asset(
                                'assets/ilustrasi.png', // ganti dengan path gambar kamu
                                height: 80,
                              ),
                            ],
                          ),
                        ) 
                      ),
                      SizedBox(height: 16),
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton.icon(
                          onPressed: () {
                            ref.read(globalStateProvider.notifier).logout();
                            Navigator.pushReplacementNamed(context, '/login');
                          },
                          icon: const Icon(
                            Icons.logout,
                            size: 18,
                            color: Colors.white,
                          ),
                          label: const Text(
                            'Logout',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 14,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.green, // hijau tosca
                            padding: const EdgeInsets.symmetric(
                              horizontal: 20,
                              vertical: 12,
                            ),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(30),
                            ),
                            elevation: 0, // tanpa shadow
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            bottomNavigationBar: BottomNavigationBar(
              selectedItemColor: Colors.teal,
              unselectedItemColor: Colors.black54,
              currentIndex: _selectedIndex,
              onTap: _onItemTapped, // di sinilah event klik ditangani
              type: BottomNavigationBarType.fixed, // <== Tambahkan ini
              items: const [
                BottomNavigationBarItem(
                  icon: Icon(Icons.home),
                  label: 'Beranda',
                ),
                BottomNavigationBarItem(
                  icon: Icon(Icons.schedule),
                  label: 'Jadwal',
                ),
                BottomNavigationBarItem(
                  icon: Icon(Icons.calendar_today),
                  label: 'Aktivitas',
                ),
                BottomNavigationBarItem(
                  icon: Icon(Icons.notifications),
                  label: 'Notifikasi',
                ),
              ],
            ),
          )
        : Scaffold(body: SplashScreen(auth.loggedIn));
  }
}

class IconLabel extends StatelessWidget {
  final IconData icon;
  final String label;
  final VoidCallback? onPressed;

  const IconLabel({
    required this.icon,
    required this.label,
    this.onPressed, // optional
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onPressed,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          CircleAvatar(
            backgroundColor: Colors.black,
            child: Icon(icon, color: Colors.white),
          ),
          const SizedBox(height: 4),
          Text(
            label,
            textAlign: TextAlign.center,
            style: const TextStyle(fontSize: 12),
          ),
        ],
      ),
    );
  }
}
