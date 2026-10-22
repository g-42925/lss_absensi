import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../providers/global_state.dart';
import '../env/env.dart';
import 'package:flutter_image_compress/flutter_image_compress.dart';
import 'package:http_parser/http_parser.dart'; // <-- MediaType
import 'package:image_picker/image_picker.dart';
import 'dart:io';


class LeaveApplyPage extends ConsumerStatefulWidget {
  const LeaveApplyPage({super.key});

  @override
  ConsumerState<LeaveApplyPage> createState() => _LeaveApplyPageState();
}

class _LeaveApplyPageState extends ConsumerState<LeaveApplyPage> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _reasonController = TextEditingController();
  final ImagePicker _picker = ImagePicker();

  XFile? _image;
  
  String? selectedValue;

  final List<Map<String, String>> typeList = [
    {'id': '68cf640082xxx', 'value': 'Cuti'},
    {'id': '68cf64008yyy', 'value': 'Sakit'},
  ];

  DateTime? tanggalMulai;
  DateTime? tanggalSelesai;

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

  Future<void> _selectDate(BuildContext context, bool isStartDate) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now(),
      firstDate: DateTime.now(),
      lastDate: DateTime(2101),
    );
    if (picked != null &&
        picked != (isStartDate ? tanggalMulai : tanggalSelesai)) {
      setState(() {
        if (isStartDate) {
          tanggalMulai = picked;
        } else {
          tanggalSelesai = picked;
        }
      });
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

  void _submitForm(int quota) async {
    final other = ref.read(globalStateProvider).other;
    final company = ref.read(globalStateProvider).company;
    final Duration d = tanggalSelesai!.difference(tanggalMulai!);
    final xTanggalMulai = DateFormat("yyyy-MM-dd").format(tanggalMulai!);
    final xTanggalSelesai = DateFormat("yyyy-MM-dd").format(tanggalSelesai!);
    final file = File(_image!.path);
    final fileName = '${DateTime.now().millisecondsSinceEpoch}';
    final url = Uri.parse("${Env.api}/api/mobile/leave");
    final headers = {"Content-type": "application/json"};
    final uploadUrl = Uri.parse("${Env.api}/filebase/upload/$fileName");

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

    final selected = selectedValue == "Sakit" ? "s" : "c";

    final params = {
      'company_id': company.id,
      'tanggal_request': xTanggalMulai,
      'tanggal_request_end': xTanggalSelesai,
      'catatan_awal': _reasonController.text,
      'pegawai_id': other.pegawaiId,
      'image':uploadResponse,
      'tipe_request' : selected
    };

    try {
      await http.post(url, headers: headers, body: jsonEncode(params));
      Navigator.pushReplacementNamed(context, '/');
    } 
    catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text("gagal mengajukan cuti!"),
          duration: Duration(seconds: 2),
        ),
      );
    }

    // if (d.inDays + 1 > quota.toDouble()) {
    //   showModalBottomSheet(
    //     context: context,
    //     isScrollControlled: true, // supaya bisa atur tinggi
    //     shape: const RoundedRectangleBorder(
    //       borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
    //     ),
    //     builder: (context) {
    //       return FractionallySizedBox(
    //         widthFactor: 1.0,
    //         heightFactor: 0.5, // setengah layar
    //         child: Padding(
    //           padding: const EdgeInsets.all(20),
    //           child: Column(
    //             mainAxisAlignment: MainAxisAlignment.center,
    //             children: [
    //               const Icon(Icons.error_outline, color: Colors.red, size: 60),
    //               const SizedBox(height: 20),
    //               Text(
    //                 "Tidak bisa melebihi sisa jumlah cuti",
    //                 textAlign: TextAlign.center,
    //                 style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
    //               ),
    //               const SizedBox(height: 8),
    //               const Text(
    //                 'Periksa kembali',
    //                 textAlign: TextAlign.center,
    //                 style: TextStyle(fontSize: 16, color: Colors.black54),
    //               ),
    //             ],
    //           ),
    //         ),
    //       );
    //     },
    //   );
    // } else {
    //   final params = {
    //     'company_id': company.id,
    //     'tanggal_request': xTanggalMulai,
    //     'tanggal_request_end': xTanggalSelesai,
    //     'catatan_awal': _reasonController.text,
    //     'pegawai_id': other.pegawaiId,
    //   };

    //   try {
    //     await http.post(url, headers: headers, body: jsonEncode(params));
    //     Navigator.pushReplacementNamed(context, '/');
    //   } catch (e) {
    //     ScaffoldMessenger.of(context).showSnackBar(
    //       SnackBar(
    //         content: Text("gagal mengajukan cuti!"),
    //         duration: Duration(seconds: 2),
    //       ),
    //     );
    //   }
    // }
  }

  @override
  Widget build(BuildContext context) {
    final int quota = ModalRoute.of(context)!.settings.arguments as int;
    final isKeyboardVisible = MediaQuery.of(context).viewInsets.bottom > 0;


    return Scaffold(
      appBar: AppBar(title: const Text('Form Pengajuan Cuti')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: <Widget>[
              GestureDetector(
                onTap: () => _selectDate(context, true),
                child: AbsorbPointer(
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
              ),
              const SizedBox(height: 16),
              GestureDetector(
                onTap: () => _selectDate(context, false),
                child: AbsorbPointer(
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
              ),
              const SizedBox(height: 16),
              
              TextFormField(
                controller: _reasonController,
                maxLines: 4,
                decoration: const InputDecoration(
                  labelText: 'Alasan Cuti',
                  hintText: 'Jelaskan alasan pengajuan cuti Anda...',
                  border: OutlineInputBorder(),
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Alasan cuti tidak boleh kosong';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 12),
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
                SizedBox(height:12),
                Container(
                  margin: const EdgeInsets.all(0),
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: Colors.grey.shade400),
                  ),
                  child: DropdownButton<String>(
                    value: selectedValue,
                    hint: const Text('Pilih jenis pengajuan'),
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
              SizedBox(height:12),
              ElevatedButton(
                onPressed: () {
                  _submitForm(quota);
                },
                style: ElevatedButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
                child: const Text(
                  'Ajukan Cuti',
                  style: TextStyle(fontSize: 16),
                ),
              ),
              SizedBox(height:16),
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
    );
  }
}
