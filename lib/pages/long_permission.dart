import 'dart:convert';
import 'dart:io';

import 'package:absensiart';
import 'package:absensi/global_state.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:http/http.dart' as http;
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:flutter_image_compress/flutter_image_compress.dart';
import 'package:http_parser/http_parser.dart'; // <-- MediaType


class LongPermissionPage extends ConsumerStatefulWidget {
  const LongPermissionPage({super.key});

  @override
  ConsumerState<LongPermissionPage> createState() => _LongPermissionPageState();
}

class _LongPermissionPageState extends ConsumerState<LongPermissionPage> {
  final TextEditingController reason = TextEditingController();
  final dateFormat = DateFormat("yyyy-MM-dd");
  final url = Uri.parse("${Env.api}/api/mobile/afp");

  String selectedEntries = '';

  DateTime? tanggalMulai;
  DateTime? tanggalSelesai;

  String? selectedValue;

  final ImagePicker _picker = ImagePicker();

  XFile? _image;

  Future<void> _pickImage(ImageSource source) async {
    final XFile? image = await _picker.pickImage(source: source);
    if (image != null) {
      setState(() {
        _image = image;
      });
    }
  }

  String formatTanggal(DateTime? date) {
    if (date == null) return "Pilih Tanggal";
    return "${date.day}/${date.month}/${date.year}";
  }

  final entries = [
    DropdownMenuEntry(value: 'i', label: 'Izin'),
    DropdownMenuEntry(value: 's', label: 'Sakit'),
  ];

  void sendRequest(String companyId, String pegawaiId) async {
    final headers = {"Content-type": "application/json"};

    final file = File(_image!.path);
		final bytes = await file.readAsBytes();
    final fileName = '${DateTime.now().millisecondsSinceEpoch}_${_image!.name}';
    final uploadUrl = Uri.parse("${Env.api}/filebase/upload/$fileName");

		print(uploadUrl);

    final compressed = await FlutterImageCompress.compressWithList(
      bytes,
      minWidth: 1080,
      minHeight: 1920,
      quality: 50,
      format: CompressFormat.jpeg, // penting, karena png lebih besar
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

		print(uploadResponse);

    final params = {
      'company_id': companyId,
      'tanggal_request': dateFormat.format(tanggalMulai!),
      'tanggal_request_end': dateFormat.format(tanggalSelesai!),
      'tipe_request': selectedEntries,
      'catatan_awal': reason.text,
      'pegawai_id': pegawaiId,
      'image': uploadResponse,
    };

    try {
      await http.post(url, headers: headers, body: jsonEncode(params));
      Navigator.pushReplacementNamed(context, '/');
    } 
		catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text("gagal mengajukan izin!"),
          duration: Duration(seconds: 2),
        ),
      );
      Navigator.pushReplacementNamed(
				context, 
				'/'
			);
    }
  }

  Future<void> pilihTanggal(BuildContext context, bool isMulai) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime(2100),
    );
    if (picked != null) {
      setState(() {
        if (isMulai) {
          tanggalMulai = picked;
        } else {
          tanggalSelesai = picked;
        }
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final isKeyboardVisible = MediaQuery.of(context).viewInsets.bottom > 0;

    final globalState = ref.read(globalStateProvider);
    final company = globalState.company;
    final other = globalState.other;
    return Scaffold(
      appBar: AppBar(title: const Text("Izin Hari")),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            Row(
              children: [
                Expanded(
                  child: GestureDetector(
                    onTap: () => pilihTanggal(context, true),
                    child: InputDecorator(
                      decoration: const InputDecoration(
                        labelText: "Pada tanggal",
                        border: OutlineInputBorder(),
                      ),
                      child: Text(formatTanggal(tanggalMulai)),
                    ),
                  ),
                ),
                SizedBox(width: 8),
                Expanded(
                  child: GestureDetector(
                    onTap: () => pilihTanggal(context, false),
                    child: InputDecorator(
                      decoration: const InputDecoration(
                        labelText: "Sampai tanggal",
                        border: OutlineInputBorder(),
                      ),
                      child: Text(formatTanggal(tanggalSelesai)),
                    ),
                  ),
                ),
              ],
            ),
            SizedBox(height: 12),
            DropdownMenu<String>(
              width: MediaQuery.of(context).size.width - 32,
              hintText: "Kategori",
              dropdownMenuEntries: entries,
              onSelected: (value) async {
                setState(() {
                  selectedEntries = value!;
                });
              },
            ),
            const SizedBox(height: 16),
            TextFormField(
              controller: reason,
              decoration: const InputDecoration(
                labelText: "Keperluan",
                border: OutlineInputBorder(),
              ),
              maxLines: 3,
            ),
            Container(
              margin: const EdgeInsets.all(0),
              padding: const EdgeInsets.symmetric(horizontal: 12),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.grey.shade400),
              ),
            ),
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
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: () {
                  sendRequest(company.id, other.pegawaiId);
                },
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: const Text("Kirim Permintaan"),
              ),
            ),
            SizedBox(height: 24),
            if (_image != null && !isKeyboardVisible)
              Center(
                child: Image.file(
                  File(_image!.path),
                  width: double.infinity,
                  height: 250,
                  fit: BoxFit.cover,
                ),
              ),
          ],
        ),
      ),
    );
  }
}
