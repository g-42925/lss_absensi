import 'dart:convert';
import 'dart:io';

import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

class ExceptionEditPage extends ConsumerStatefulWidget {
  const ExceptionEditPage({super.key});

  @override
  ConsumerState<ExceptionEditPage> createState() => _ExceptionEditPageState();
}

class _ExceptionEditPageState extends ConsumerState<ExceptionEditPage> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _reasonController = TextEditingController();
  DateTime? _selectedDate;
  String? selectedValue;
  final ImagePicker _picker = ImagePicker();

  bool _isInitialized = false;

  XFile? _image;

  final List<Map<String, String>> typeList = [
    {'id': '68cf640082e8x', 'value': 'Absen masuk'},
    {'id': '68cf64008yyy', 'value': 'Absen pulang'},
    {'id': '8474832hd83xx', 'value': 'Lupa absen'}
    
  ];

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

  Future<void> _deleteForm(String id) async {
    final url = Uri.parse("${Env.api}/api/mobile/exceptionDelete/$id");
    final headers = {"Content-type": "application/json"};

    try {
      final exc = await http.get(url, headers: headers);
      Navigator.pop(context);
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text("gagal menghapus pengecualian!"),
          duration: Duration(seconds: 2),
        ),
      );
    } finally {}
  }

  void _submitForm(String pegawaiId, Map<String, dynamic> args) async {
    final url = Uri.parse("${Env.api}/api/mobile/exceptionEdit");

    final headers = {"Content-type": "application/json"};

    final date = _selectedDate != null
        ? DateFormat('yyyy-MM-dd').format(_selectedDate!)
        : args['date'];
    final type = selectedValue ?? args['type'];

    if ((_formKey.currentState!.validate() && _selectedDate != null) ||
        args != null) {
      if (_image != null) {
        final file = File(_image!.path);
        final fileName =
            '${DateTime.now().millisecondsSinceEpoch}_${_image!.name}';
        final supabase = Supabase.instance.client;

        await supabase.storage.from('storage').upload(fileName, file);

        final excType = selectedValue ?? args['type'];

        final uploaded = supabase.storage
            .from('storage')
            .getPublicUrl(fileName);

        final exceptionData = {
          "date": date,
          "reason": _reasonController.text,
          "employee_id": pegawaiId,
          "type": excType,
          "image": uploaded,
          "id": args['id'],
        };
        try {
          final exc = await http.post(
            url,
            headers: headers,
            body: jsonEncode(exceptionData),
          );

          if (jsonDecode(exc.body)['success']) {
            Navigator.pushNamedAndRemoveUntil(context, '/', (route) => false);
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text("coba beberapa saat lagi"),
                duration: Duration(seconds: 2),
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
        final excType = selectedValue ?? args['type'];

        final exceptionData = {
          "date": date,
          "reason": _reasonController.text,
          "employee_id": pegawaiId,
          "type": excType,
          "image": args['image'],
          "id": args["id"],
        };
        try {
          final exc = await http.post(
            url,
            headers: headers,
            body: jsonEncode(exceptionData),
          );

          if (jsonDecode(exc.body)['success']) {
            Navigator.pushNamedAndRemoveUntil(context, '/', (route) => false);
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text("coba beberapa saat lagi"),
                duration: Duration(seconds: 2),
              ),
            );

            Navigator.pop(context);
          }
        } catch (e) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text("gagal mengajukan pengecualian!"),
              duration: Duration(seconds: 2),
            ),
          );

          Navigator.pop(context);
        }
      }
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Harap lengkapi data terlebih dahulu")),
      );
    }
  }

  Future<void> _pickImage(ImageSource source) async {
    final XFile? image = await _picker.pickImage(source: source);
    if (image != null) {
      setState(() {
        _image = image;
      });
    }
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (!_isInitialized) {
      final args =
          ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;
      _reasonController.text = args['reason'] ?? '';
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
    final args =
        ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;

    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;

    final isKeyboardVisible = MediaQuery.of(context).viewInsets.bottom > 0;

    return Scaffold(
      appBar: AppBar(title: Text("Edit Pengecualian")),
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
                    _selectedDate == null
                        ? args['date']
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
                  labelText: "Alasan",
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return "Alasan tidak boleh kosong";
                  }
                  return null;
                },
              ),
              SizedBox(height: 12),
              Container(
                margin: const EdgeInsets.all(0),
                padding: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.grey.shade400),
                ),
                child: DropdownButton<String>(
                  value: selectedValue ?? args['type'],
                  hint: const Text('Pilih jenis pengecualiann'),
                  isExpanded: true, // biar lebar mengikuti parent
                  underline: const SizedBox(), // hilangkan garis bawaan
                  items: typeList.map((Map<String, String> r) {
                    return DropdownMenuItem<String>(
                      value: r['value'],
                      child: Text(r['value']!),
                    );
                  }).toList(),
                  onChanged: (value) {
                    setState(() {
                      selectedValue = value;
                    });
                  },
                ),
              ),
              SizedBox(height: 12),
              Container(
                margin: const EdgeInsets.all(8),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Expanded(
                      child: ElevatedButton.icon(
                        onPressed: () => _pickImage(ImageSource.gallery),
                        icon: const Icon(Icons.photo_library),
                        label: const Text('From Galery'),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: ElevatedButton.icon(
                        onPressed: () => _pickImage(ImageSource.camera),
                        icon: const Icon(Icons.camera_alt),
                        label: const Text('New Image'),
                      ),
                    ),
                  ],
                ),
              ),
              // Tombol Submit
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  icon: Icon(Icons.send),
                  label: Text("Edit"),
                  onPressed: () => {_submitForm(other.pegawaiId, args)},
                ),
              ),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  child: Text("Hapus"),
                  onPressed: () => {_deleteForm(args['id'])},
                ),
              ),
              SizedBox(height: 24),
              if ((args['image'] != null && _image == null) &&
                  !isKeyboardVisible)
                Center(
                  child: Image.network(
                    args['image'],
                    width: double.infinity,
                    height: 250,
                    fit: BoxFit.cover,
                  ),
                ),
              if (_image != null && !isKeyboardVisible)
                Center(
                  child: Image.file(
                    File(_image!.path),
                    width: double.infinity,
                    height: 250,
                    fit: BoxFit.cover,
                  ),
                )
              else if (!isKeyboardVisible && args['image'] == null)
                const Text('Silahkan tambahkan bukti pengajuan'),
            ],
          ),
        ),
      ),
    );
  }
}
