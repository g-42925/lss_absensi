import 'dart:convert';
import 'package:timeago/timeago.dart' as timeago;
import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;

class EmployeesPage extends ConsumerStatefulWidget {
  const EmployeesPage({super.key});

  @override
  ConsumerState<EmployeesPage> createState() => _EmployeesPageState();
}

class _EmployeesPageState extends ConsumerState<EmployeesPage> {
  late Future<http.Response>? list;

  Future<http.Response> getEmployeeList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse(
      "${Env.api}/api/mobile/employeeList/${other.pegawaiId}",
    );

    final r = http.get(url);

    try {
      await r;
    } catch (e) {
      print("error disini");
      print(e);
    }

    return r;
  }

  Future<void> fetch() async {
    setState(() {
      list = null;
    });
    setState(() {
      list = getEmployeeList();
    });
  }

  @override
  void initState() {
    super.initState();
    fetch();
  }

  @override
  Widget build(BuildContext context) {
    final globalState = ref.read(globalStateProvider);
    final pp = globalState.other.fotoPegawai;

    return Scaffold(
      appBar: AppBar(title: const Text('Karyawan'), centerTitle: true),
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
                      padding: const EdgeInsets.all(12),
                      itemCount: data.length,
                      itemBuilder: (context, index) {
                        final emp = data[index];
                        return Card(
                          elevation: 3,
                          margin: const EdgeInsets.symmetric(vertical: 8),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: ListTile(
                            contentPadding: const EdgeInsets.symmetric(
                              horizontal: 16,
                              vertical: 10,
                            ),
                            title: Text(
                              emp['nama_pegawai'],
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w600,
                                color: Colors.black87,
                              ),
                            ),
                            subtitle: Text(
                              emp['name'],
                              style: const TextStyle(
                                fontSize: 13,
                                color: Colors.black54,
                              ),
                            ),
                            trailing: ClipRRect(
                              borderRadius: BorderRadius.circular(
                                24,
                              ), // atur rounded
                              child: Image.network(
                                "https://t4.ftcdn.net/jpg/00/97/00/09/240_F_97000905_fsWItLsupPxPXA5yhpDixzZ69xmn3MbZ.jpg", // Ganti dengan path image kamu
                                height: 45,
                              ),
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
