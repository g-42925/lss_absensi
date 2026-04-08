import 'dart:convert';

import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;

class TaskEditPage extends ConsumerStatefulWidget {
  const TaskEditPage({super.key});

  @override
  ConsumerState<TaskEditPage> createState() => _TaskEditPageState();
}

class _TaskEditPageState extends ConsumerState<TaskEditPage> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _reasonController = TextEditingController();
  DateTime? _selectedDate;
  bool _isInitialized = false;

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

  void _submitForm(String taskId, String date) async {
    final url = Uri.parse("${Env.api}/api/mobile/taskEdit");

    final headers = {"Content-type": "application/json"};

    if (_formKey.currentState!.validate()) {
      final exceptionData = {
        "date": _selectedDate != null
            ? _selectedDate!.toIso8601String().split("T")[0]
            : date,
        "description": _reasonController.text,
        "task_id": taskId,
      };

      print(exceptionData);

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
              duration: Duration(seconds: 10),
            ),
          );

          Navigator.pop(context);
        }
      } catch (e) {
        print(e);

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("gagal melakukan edit data pengecualian!"),
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
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (!_isInitialized) {
      final args =
          ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;
      _reasonController.text = args['desc'] ?? '';
      _isInitialized = true;
    }
  }

  @override
  void dispose() {
    _reasonController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    final args =
        ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;

    return Scaffold(
      appBar: AppBar(title: Text("Form Edit Tugas")),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Pilih tanggal
              Text("Tanggal"),
              SizedBox(height: 8),
              InkWell(
                onTap: () => _pickDate(context),
                child: InputDecorator(
                  decoration: InputDecoration(
                    border: OutlineInputBorder(),
                    contentPadding: EdgeInsets.all(12),
                  ),
                  child: Text(
                    _selectedDate != null
                        ? "${_selectedDate!.toLocal()}".split(" ")[0]
                        : args['date'],
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
                  onPressed: () => {_submitForm(args['id'], args['date'])},
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
