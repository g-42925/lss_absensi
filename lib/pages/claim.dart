import 'dart:convert';

import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;

class ClaimPage extends ConsumerStatefulWidget {
  const ClaimPage({super.key});

  @override
  ConsumerState<ClaimPage> createState() => _ExceptionPageState();
}

class _ExceptionPageState extends ConsumerState<ClaimPage> {
  late Future<http.Response>? list;

  Future<http.Response> getLeaveList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse("${Env.api}/api/mobile/claim/${other.pegawaiId}");

    return http.get(url);
  }

  Future<void> fetch() async {
    setState(() {
      list = null;
    });
    setState(() {
      list = getLeaveList();
    });
  }

  Map<String, dynamic> getStatusColor(String status) {
    switch (status) {
      case "pending":
        return {"status": "pending", "color": Colors.orange};
      case "approved":
        return {"status": "approved", "color": Colors.green};
      case "rejected":
        return {"status": "rejected", "color": Colors.red};
      default:
        return {"status": "pending", "color": Colors.orange};
    }
  }

  @override
  void initState() {
    super.initState();
    fetch();
  }

  void getReimburseList(String id) async {
    final url = Uri.parse("${Env.api}/api/mobile/reimburseList/$id");

    try {
      final response = await http.get(url);
      final result = jsonDecode(response.body);

      print(result);

      Navigator.pushNamed(
        context,
        '/claim_submit',
        arguments: {'result': result},
      );
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Coba beberapa saat lagi'),
          duration: Duration(seconds: 2),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final company = ref.read(globalStateProvider).company;
    return Scaffold(
      appBar: AppBar(title: Text("Reimburse")),
      body: list != null
          ? FutureBuilder(
              future: list,
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return Center(child: CircularProgressIndicator());
                }
                if (snapshot.hasError) {
                  return Center(child: Text("something went wrong"));
                } else {
                  final response = snapshot.data!;
                  print(response.body);
                  final data = jsonDecode(response.body);
                  if (data['success'] as bool) {
                    return ListView.builder(
                      itemCount: (data['result'] as List).length,
                      itemBuilder: (context, index) {
                        final item = (data['result'] as List)[index];
                        return Card(
                          margin: EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 6,
                          ),
                          elevation: 3,
                          child: ListTile(
                            leading: Icon(
                              Icons.event_note,
                              color: getStatusColor(item['status'])['color'],
                            ),
                            title: Text(
                              "Tanggal: ${item['date']}",
                              style: TextStyle(fontWeight: FontWeight.bold),
                            ),
                            subtitle: Text(
                              "${item['reimburse_name']} : ${item['value']}",
                            ),
                            trailing: Text(
                              getStatusColor(
                                item['status'],
                              )['status'].toString().toUpperCase(),
                              style: TextStyle(
                                color: getStatusColor(item['status'])['color'],
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        );
                      },
                    );
                  } else {
                    return Center(
                      child: Text("something went wrong or no data exist"),
                    );
                  }
                }

                return SizedBox();
              },
            )
          : SizedBox(),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          getReimburseList(company.id);
          //Navigator.pushNamed(context, '/claim_submit');
        },
        tooltip: 'Tambah',
        child: const Icon(Icons.add),
      ),
    );
  }
}
