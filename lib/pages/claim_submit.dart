import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:absensi/env/env.dart';
import 'package:absensi/providers/global_state.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:http/http.dart' as http;
import 'package:supabase_flutter/supabase_flutter.dart';

class ClaimSubmitPage extends ConsumerStatefulWidget {
  const ClaimSubmitPage({super.key});

  @override
  ConsumerState<ClaimSubmitPage> createState() => _ClaimSubmitPageState();
}

class _ClaimSubmitPageState extends ConsumerState<ClaimSubmitPage> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController valueController = TextEditingController();
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

  void _submitForm(String pegawaiId) async {
    if (_image == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Silahkan upload foto bukti terlebih dahulu")),
      );
      return;
    }

    if (!_formKey.currentState!.validate()) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Harap lengkapi data terlebih dahulu")),
      );
      return;
    }

    final url = Uri.parse("${Env.api}/api/mobile/makeclaim");
    final supabase = Supabase.instance.client;

    final file = File(_image!.path);
    final fileName = '${DateTime.now().millisecondsSinceEpoch}_${_image!.name}';

    await supabase.storage.from('storage').upload(fileName, file);
    final uploaded = supabase.storage.from('storage').getPublicUrl(fileName);

    final payload = {
      "value": valueController.text,
      "employee_id": pegawaiId,
      "reimburse_id": selectedValue,
      "photo": uploaded,
    };

    try {
      final exc = await http.post(
        url,
        headers: {"Content-type": "application/json"},
        body: jsonEncode(payload),
      );

      final res = jsonDecode(exc.body);

      if (res['success'] == true) {
        Navigator.pushNamedAndRemoveUntil(
          context,
          '/',
          (Route<dynamic> route) => false,
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("Coba beberapa saat lagi")),
        );
      }
    } catch (e) {
      print(e);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Gagal mengajukan pengajuan!")),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;

    final args =
        ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;

    final List<Map<String, dynamic>> resultList =
        List<Map<String, dynamic>>.from(args['result']);
    
    print(resultList);

    final List<Map<String, String>> reimburseList = resultList
        .map(
          (n) => {
            'value': n['reimburse_id'].toString(),
            'name': n['reimburse_name'].toString(),
          },
        )
        .toList();

    final isKeyboardVisible = MediaQuery.of(context).viewInsets.bottom > 0;

    return Scaffold(
      appBar: AppBar(title: Text("Ajukan Reimburse")),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [

                  // Dropdown label
                  Padding(
                    padding: EdgeInsets.symmetric(vertical: 8),
                    child: Text("Jenis Reimburse"),
                  ),

                  // FIX: dropdown tidak tertutupi oleh widget lain
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: Colors.grey.shade400),
                    ),
                    child: DropdownButton<String>(
                      value: selectedValue,
                      isExpanded: true,
                      underline: SizedBox(),
                      hint: Text("Pilih jenis reimburses"),
                      items: reimburseList.map((r) {
                        return DropdownMenuItem<String>(
                          value: r['value'],
                          child: Text(r['name']!),
                        );
                      }).toList(),
                      onChanged: (value) {
                        setState(() {
                          selectedValue = value;
                        });
                      },
                    ),
                  ),

                  SizedBox(height: 16),

                  // Input jumlah
                  TextFormField(
                    controller: valueController,
                    maxLines: 3,
                    decoration: InputDecoration(
                      labelText: "Jumlah",
                      border: OutlineInputBorder(),
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return "Jumlah tidak boleh kosong";
                      }
                      return null;
                    },
                  ),

                  SizedBox(height: 16),

                  // Tombol upload gambar
                  Row(
                    children: [
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: () => _pickImage(ImageSource.gallery),
                          icon: Icon(Icons.photo_library),
                          label: Text('From Gallery'),
                        ),
                      ),
                      SizedBox(width: 10),
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: () => _pickImage(ImageSource.camera),
                          icon: Icon(Icons.camera_alt),
                          label: Text('New Image'),
                        ),
                      ),
                    ],
                  ),

                  SizedBox(height: 16),

                  // Tombol submit
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      icon: Icon(Icons.send),
                      label: Text("Kirim Pengajuan"),
                      onPressed: () => _submitForm(other.pegawaiId),
                    ),
                  )
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
