import 'dart:convert';

import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;

class TaskAddPage extends ConsumerStatefulWidget {
  const TaskAddPage({super.key});

  @override
  ConsumerState<TaskAddPage> createState() => _TaskAddPageState();
}

class _TaskAddPageState extends ConsumerState<TaskAddPage> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _reasonController = TextEditingController();
  DateTime? _selectedDate;

  Future<void> _pickDate(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2024),
      lastDate: DateTime(2030),
    );
    if (picked != null && picked != _selectedDate) {
      setState(() {
        _selectedDate = picked;
      });
    }
  }

  void _submitForm(String pegawaiId) async {
    final url = Uri.parse("${Env.api}/api/mobile/makeTask");

    final headers = {"Content-type": "application/json"};

    if (_formKey.currentState!.validate() && _selectedDate != null) {
      final exceptionData = {
        "date": _selectedDate!.toIso8601String().split("T")[0],
        "description": _reasonController.text,
        "employee_id": pegawaiId,
      };

      try {
        final exc = await http.post(
          url,
          headers: headers,
          body: jsonEncode(exceptionData),
        );

        print(exc.body);

        if (jsonDecode(exc.body)['success']) {
          Navigator.pushNamedAndRemoveUntil(context, '/', (route) => false);
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text("coba beberapa saat lagi"),
              duration: Duration(seconds: 30),
            ),
          );

          Navigator.pop(context);
        }
      } catch (e) {
        print(e);

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("gagal mengajukan pengecualian!"),
            duration: Duration(seconds: 2),
          ),
        );

        Navigator.pop(context);
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Harap lengkapi data terlebih dahulu")),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;

    return Scaffold(
      appBar: AppBar(title: Text("Form Tugas")),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Pilih tanggal
              SizedBox(height: 8),
              InkWell(
                onTap: () => _pickDate(context),
                child: InputDecorator(
                  decoration: InputDecoration(
                    border: OutlineInputBorder(),
                    contentPadding: EdgeInsets.all(12),
                  ),
                  child: Text(
                    _selectedDate == null
                        ? "Pilih tanggal"
                        : "${_selectedDate!.toLocal()}".split(" ")[0],
                  ),
                ),
              ),
              SizedBox(height: 16),

              // Alasan eksepsi
              TextFormField(
                controller: _reasonController,
                maxLines: 3,
                decoration: InputDecoration(
                  labelText: "Tempat Penugasan",
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return "Alasan tidak boleh kosong";
                  }
                  return null;
                },
              ),
              SizedBox(height: 24),

              // Tombol Submit
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  icon: Icon(Icons.send),
                  label: Text("Kirim Pengajuan"),
                  onPressed: () => {_submitForm(other.pegawaiId)},
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
