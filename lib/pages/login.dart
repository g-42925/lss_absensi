import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import '../providers/global_state.dart';
import '../env/env.dart';

class LoginPage extends ConsumerStatefulWidget {
  const LoginPage({super.key});

  @override
  ConsumerState<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends ConsumerState<LoginPage> {
  final TextEditingController emailController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final url = Uri.parse("${Env.api}/api/mobile/loginv2");

  bool visibility = false;
  bool loading = false;

  setVisible() {
    setState(() {
      visibility = !visibility;
    });
  }

  login() async {
    setState(() {
      loading = true;
    });

    Map<String, String> credential = {
      'email': emailController.text,
      'pwd': passwordController.text,
    };

    Map<String, String> headers = {'Content-Type': 'application/json'};

    try {
      final response = await http.post(
        url,
        headers: headers,
        body: jsonEncode(credential),
      );

      print(response.body);

      // if (response.statusCode == 200) {
      final responseBody = jsonDecode(response.body);

      final String workSystem = responseBody['result']['workSystem'];

      final start = workSystem == "shift"
          ? responseBody['result']['clock_in']
          : responseBody['result']['jam_masuk'];

      final nextStart = workSystem == "shift"
          ? responseBody['result']['clock_in']
          : responseBody['result']['next']['jam_masuk'];

      final finish = workSystem == "shift"
          ? responseBody['result']['clock_out']
          : responseBody['result']['jam_pulang'];
      final workDay = workSystem == "shift"
          ? responseBody['result']['workDay']
          : responseBody['result']['workDay'];
      final workSystemName = workSystem == "shift"
          ? responseBody['result']['workSystemName']
          : responseBody['result']['workSystemName'];

      final breakStart = workSystem == "shift"
          ? responseBody['result']['break']
          : responseBody['result']['jam_istirahat'];

      final breakEnd = workSystem == "shift"
          ? responseBody['result']['after_break']
          : responseBody['result']['selesai_istirahat'];

      final Company company = (
        id: responseBody['result']['company_id'] ?? '',
        name: responseBody['result']['company_name'] ?? '',
        logo: responseBody['result']['logo'] ?? '',
        address: responseBody['result']['address'] ?? '',
        salaryDate: int.tryParse(responseBody['result']['salary_date']?.toString() ?? '0') ?? 0,
      );

      final Schedule schedule = (
        start: start ?? '',
        nextStart: nextStart ?? '',
        finish: finish ?? '',
        breakStart: breakStart,
        breakFinish: breakEnd,
        workSystem: workSystem,
        workSystemName: workSystemName,
      );

      final locationsRaw = responseBody['result']['locations'] ?? [];

      final Iterable<Map<String, dynamic>> location =
          (locationsRaw as List).map((l) {
        return {
          'lat': l['garis_lintang']?.toString() ?? '',
          'lon': l['garis_bujur']?.toString() ?? '',
          'address': l['alamat_lokasi'] ?? '',
          'locationName': l['nama_lokasi'] ?? '',
        };
      });

      final Other other = (
        pegawaiId: responseBody['result']['pegawai_id'] ?? '',
        namaPegawai: responseBody['result']['nama_pegawai'] ?? '',
        nomorPegawai: responseBody['result']['nomor_pegawai'] ?? '',
        emailPegawai: responseBody['result']['email_pegawai'] ?? '',
        fotoPegawai: responseBody['result']['foto_pegawai'] ?? '',
        position: responseBody['result']['position'] ?? '',
        status: responseBody['result']['status_pegawai'] ?? '',
      );

      final Holiday holiday = (
        holiday: responseBody['result']['holiday'] ?? false,
        workDay: workDay,
      );


      final Status status = (signedIn: false, signedOut: false);

      final Auth auth = (
        loggedIn: true,
        date: DateTime.now().toIso8601String(),
      );

      final OverWork overWork = (onOverWork: false);

      final ffocia = responseBody['result']['ffocia'] == "1" ? true : false;
      final ffocoa = responseBody['result']['ffocoa'] == "1" ? true : false;

      final coLimit = int.tryParse(responseBody['result']['co_limit']?.toString() ?? '0') ?? 0;

      final ciLimit = int.tryParse(responseBody['result']['ci_limit']?.toString() ?? '0') ?? 0;

      final tolerance = int.tryParse(responseBody['result']['tolerance']?.toString() ?? '0') ?? 0;


      final Config config = (
        ffocia: ffocia,
        ffocoa: ffocoa,
        coLimit: coLimit,
        ciLimit: ciLimit,
        tolerance: tolerance,
      );

      final XPresence presence = (
        ci:responseBody['result']['presence']['jam_masuk'],
        co:responseBody['result']['presence']['jam_keluar'],
      );

      ref.read(globalStateProvider.notifier).login((
        auth: auth,
        status: status,
        company: company,
        schedule: schedule,
        permission: (id: 0),
        location: (list: location.toList()),
        position: (lat: 0, lon: 0),
        other: other,
        history: [],
        coordinate: (lat: 0, lon: 0),
        holiday: holiday,
        breakInfo: (onBreak: false, startFrom: ''),
        overWork: overWork,
        config: config,
        task: (started: [], finished: []),
        exception: (list: []),
        csh: (allowed: false),
        reminder: (lastLat:0,lastLon:0),
        presence: presence
      ));

      Navigator.pushReplacementNamed(context, '/');
    } catch (e) {
      print("error");
      print(e);
    } finally {
      setState(() {
        loading = false;
      });
    }
  }

  @override
  void initState() {
    super.initState();
    final auth = ref.read(globalStateProvider).auth;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Center(
          child: Container(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 30),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Image.asset(
                        'assets/logo.png', // Ganti dengan path logo-mu
                        height: 120,
                      ),
                    ],
                  ),
                  const Text(
                    'Online Attendance Application',
                    style: TextStyle(
                      fontSize: 16,
                      color: Colors.grey,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'Login using your company employee account',
                    style: TextStyle(color: Colors.grey),
                  ),
                  const SizedBox(height: 30),

                  // Input Email
                  TextField(
                    controller: emailController,
                    decoration: const InputDecoration(
                      labelText: 'Email',
                      border: OutlineInputBorder(),
                    ),
                  ),
                  const SizedBox(height: 15),

                  // Input Password
                  TextField(
                    controller: passwordController,
                    obscureText: visibility ? false : true,
                    decoration: InputDecoration(
                      labelText: 'Password',
                      border: const OutlineInputBorder(),
                      suffixIcon: IconButton(
                        icon: Icon(
                          visibility ? Icons.visibility_off : Icons.visibility,
                        ),
                        onPressed: () => setVisible(),
                      ),
                    ),
                  ),

                  const SizedBox(height: 10),

                  // Forgot Password
                  Align(
                    alignment: Alignment.centerLeft,
                    child: TextButton(
                      onPressed: () {},
                      child: const Text('Forgot Password?'),
                    ),
                  ),

                  const SizedBox(height: 10),

                  // Tombol Login
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () => login(),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.teal,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 15),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(30),
                        ),
                      ),
                      child: loading
                          ? CircularProgressIndicator()
                          : Text('Login'),
                    ),
                  ),

                  const SizedBox(height: 20),

                  // Belum punya akun
                  TextButton(
                    onPressed: () {},
                    child: const Text("Don't have employee account yet?"),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
