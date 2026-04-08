import 'dart:convert';
import 'dart:io';

import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;
import 'package:image_picker/image_picker.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:flutter_image_compress/flutter_image_compress.dart';
import 'package:http_parser/http_parser.dart'; // <-- MediaType

class ExceptionAddPage extends ConsumerStatefulWidget {
  const ExceptionAddPage({super.key});

  @override
  ConsumerState<ExceptionAddPage> createState() => _ExceptionAddPageState();
}

class _ExceptionAddPageState extends ConsumerState<ExceptionAddPage> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _reasonController = TextEditingController();
  DateTime? _selectedDate;
  String? selectedValue;
  String? _selectedValue;

  final ImagePicker _picker = ImagePicker();

  XFile? _image;

  bool clicked = false;

  final List<Map<String, String>> typeList = [
    {'id': '68cf640082e8x', 'value': 'Absen masuk'},
    {'id': '8474832hd8322', 'value': 'Absen pulang'},
    {'id': '8474832hd83xx', 'value': 'Lupa absen'}
  ];

  final List<Map<String, String>> isCshOption = [
    {'id': '68cf640082ssz', 'value': 'Yes'},
    {'id': '68cf640082ess', 'value': 'No'},
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

  void _submitForm(String pegawaiId) async {
    final url = Uri.parse("${Env.api}/api/mobile/makeexception");

    final headers = {"Content-type": "application/json"};

    final exceptionList = ref.read(globalStateProvider).exception;

    if (_formKey.currentState!.validate() && _selectedDate != null) {
      final file = File(_image!.path);
      final fileName = '${DateTime.now().millisecondsSinceEpoch}';
      final fDate = _selectedDate!.toIso8601String().split("T")[0];
      final identifier = "$selectedValue-$fDate";
      final uploadUrl = Uri.parse("${Env.api}/filebase/upload/$fileName");

      if (!exceptionList.list.contains(identifier)) {
        try {
          setState(() {
            clicked = true;
          });

          final bytes = await file.readAsBytes();

          final compressed = await FlutterImageCompress.compressWithList(
            bytes,
            minWidth: 1080,
            minHeight: 1920,
            quality: 50,
            format: CompressFormat.jpeg,
          );

          final request = http.MultipartRequest('POST', uploadUrl);

          request.files.add(
            http.MultipartFile.fromBytes(
              'file', // field name
              compressed, // file data
              filename: fileName,
              contentType: MediaType('image', 'png'),
            ),
          );

          final streamedResponse = await request.send();

          if (streamedResponse.statusCode != 200) {}

          final responseBody = await streamedResponse.stream.bytesToString();

          final uploadResponse = responseBody;



          // final supabase = Supabase.instance.client;

          // await supabase.storage.from('storage').upload(fileName, file);

          // final uploaded = supabase.storage
          //     .from('storage')
          //     .getPublicUrl(fileName);

          final isCsh = _selectedValue == "Yes" ? true : false;

          final exceptionData = {
            "date": _selectedDate!.toIso8601String().split("T")[0],
            "reason": _reasonController.text,
            "employee_id": pegawaiId,
            "type": selectedValue,
            "image": uploadResponse,
            "isCsh": isCsh,
          };

          final exc = await http.post(
            url,
            headers: headers,
            body: jsonEncode(exceptionData),
          );

          if (jsonDecode(exc.body)['success']) {
            ref.read(globalStateProvider.notifier).addException(identifier);
            Navigator.pushNamedAndRemoveUntil(context, '/', (route) => false);
          } 
          else {
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
        } finally {
          setState(() {
            clicked = false;
          });
        }
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text("pengajuan pengecualian tidak valid"),
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

  Future<void> _pickImage(ImageSource source) async {
    final XFile? image = await _picker.pickImage(source: source);
    if (image != null) {
      setState(() {
        _image = image;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final isKeyboardVisible = MediaQuery.of(context).viewInsets.bottom > 0;

    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;

    return Scaffold(
      appBar: AppBar(title: Text("Ajukan Pengecualian")),
      body: SingleChildScrollView(
        child: Padding(
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
                    value: selectedValue,
                    hint: const Text('Pilih jenis pengecualian'),
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
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Expanded(
                        child: ElevatedButton(
                          onPressed: () => _pickImage(ImageSource.gallery),
                          child: const Text('From Gallery'),
                          style: ElevatedButton.styleFrom(
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(
                                4,
                              ), // ubah angka sesuai kebutuhan
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(
                                4,
                              ), // ubah angka sesuai kebutuhan
                            ),
                          ),
                          onPressed: () => _pickImage(ImageSource.camera),
                          child: const Text('Take photo'),
                        ),
                      ),
                    ],
                  ),
                ),
                SizedBox(height: 12),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: clicked
                        ? null
                        : () {
                            _submitForm(other.pegawaiId);
                            setState(() {
                              clicked = true;
                            });
                          },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: clicked ? Colors.red : Colors.green,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(
                          4,
                        ), // ubah angka sesuai kebutuhan
                      ),
                    ),
                    child: Text(
                      "Ajukan pengeculian",
                      style: TextStyle(
                        color: Colors.white, // warna teks putih
                        fontSize: 16,
                      ),
                    ),
                  ),
                ),
                SizedBox(height: 12),
                if (_image != null && !isKeyboardVisible)
                  Container(
                    width: double.infinity,
                    child: Image.file(
                      File(_image!.path),
                      width: 250,
                      height: 250,
                      fit: BoxFit.cover,
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
