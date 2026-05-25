import 'dart:math';
import 'dart:io';
import 'dart:ui';
import 'dart:convert';
import 'dart:typed_data';
import 'package:flutter_image_compress/flutter_image_compress.dart';
import 'package:geolocator/geolocator.dart';
import 'package:http_parser/http_parser.dart';
import 'package:intl/intl.dart';
import 'package:flutter/rendering.dart';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../env/env.dart';
import '../providers/global_state.dart';
import '../providers/location_provider.dart';


class SignOutPage extends ConsumerStatefulWidget {
  final CameraDescription camera;

  const SignOutPage({super.key, required this.camera});

  @override ConsumerState<SignOutPage> createState() => _SignOutPageState();
}

class _SignOutPageState extends ConsumerState<SignOutPage> {
  late CameraController _controller;
  late Future<void> _initializeControllerFuture;
  final GlobalKey _globalKey = GlobalKey();
  SupabaseClient supabase = Supabase.instance.client;
  bool preview = false;

  late Future<List<dynamic>> _future;

  double latitude = 0;
  double longitude = 0;
  String locationName = 'Anda berada di luar area presensi';

  final controller = TextEditingController();

  String selectedValue = "";

  bool clicked = false;

  Map<String, String> loc = {
    'subDistrict': '',
    'province': '',
    'country': '',
    'address': '',
  };

  String path = "";

  Future<dynamic> requestLocation() async{
    LocationPermission permission = await Geolocator.requestPermission();
    
    if(permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
    }

    return Geolocator.getCurrentPosition();
  }

  @override
  void initState() {
    super.initState();
    _controller = CameraController(widget.camera, ResolutionPreset.high);
    _initializeControllerFuture = _controller.initialize();
    _future = Future.wait([
      _initializeControllerFuture,
    ])
    .then((value) {
      return value;
    });
  }

  bool isOnOffice(double latitude, double longitude) {
    final globalState = ref.read(globalStateProvider);
    final locations = globalState.location.list;

    return locations.any((locs) {
      final lat2 = double.parse(locs['lat']);
      final lon2 = double.parse(locs['lon']);
      final distance = haversineDistance(latitude, lat2, longitude, lon2);

      return distance <= 50; // true jika ada lokasi dalam 50 meter
    });
  }

  Future<ByteBuffer> captureScreen() async {
    await Future.delayed(Duration(milliseconds: 1000));
    final boundary = _globalKey.currentContext!.findRenderObject() as RenderRepaintBoundary;
    final image = await boundary.toImage(pixelRatio: 3.0);
    final byteData = await image.toByteData(format: ImageByteFormat.png);
    return byteData?.buffer as ByteBuffer;
  }

  double toRad(double degree) {
    return degree * pi / 180;
  }

  num haversineDistance(lat1, lat2, lon1, lon2) {
    num dLat = toRad(lat2 - lat1);
    num dLon = toRad(lon2 - lon1);
    num cosinus1 = cos(toRad(lat1));
    num cosinus2 = cos(toRad(lat2));
    num cosValue = cosinus1 * cosinus2;
    num sinus1 = pow(sin(dLat / 2), 2);
    num sinus2 = pow(sin(dLon / 2), 2);
    num v = sinus1 + (cosValue * sinus2);

    double result = 6371 * (1000 * (2 * asin(sqrt(v))));

    return double.parse(result.toStringAsFixed(2));
  }

  String getYear(DateTime information) {
    return DateFormat('dd/MM/yy').format(information);
  }

  void captureAndUpload(String? pegawaiId,bool csh) async {
    final currentTime = DateTime.now();

    final uri = Uri.parse(Env.gMapUrl).replace(
      queryParameters: {'latlng': "$latitude,$longitude", 'key': Env.gMapKey},
    );

    try {
      final state = ref.read(globalStateProvider);
      final company = state.company;
      final other = state.other;

      final now = DateTime.now(); // ambil tanggal sekarang

      final img = await _controller.takePicture();
      final requestResponse = await http.get(uri);
      final response = jsonDecode(requestResponse.body);
      final target = response['results'][0];
      final addressComponents = target['address_components'];
      final fileName = '${DateTime.now().millisecondsSinceEpoch}';
      final url = Uri.parse("${Env.api}/api/mobile/signout");
      final formattedTime = DateFormat("HH:mm").format(currentTime);
      final headers = {"Content-type": "application/json"};
      final formatted = DateFormat('yyyy-MM-dd').format(now);
      final timestamp = DateFormat('yyyy-MM-dd HH:mm:ss').format(now);
      final formattedSecond = DateFormat('HH:mm:ss').format(now);
      final uploadUrl = Uri.parse("${Env.api}/filebase/upload/$fileName");

      loc['address'] = target['formatted_address'];
      loc['subDistrict'] = addressComponents[3]['short_name'];
      loc['province'] = addressComponents[5]['short_name'];
      loc['country'] = addressComponents[6]['long_name'];

      setState(() {
        path = img.path;
        preview = true;
      });

      final result = await captureScreen();

      final bytes = result.asUint8List();

      final compressed = await FlutterImageCompress.compressWithList(
        bytes,
        minWidth: 1080,
        minHeight: 1920,
        quality: 50,
        format: CompressFormat.jpeg, // penting, karena png lebih besar
      );

      showDialog(
        context: context,
        barrierDismissible: true,
        builder: (_) => Dialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8), // ubah sesuai selera
          ),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Row(
              children: [
                CircularProgressIndicator(),
                SizedBox(width: 20),
                Text('submit is loading, please wait...'),
              ],
            ),
          ),
        ),
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

      final params = {
        "jam_keluar": formattedTime,
        "foto_absen_keluar": uploadResponse,
        "latitude_keluar": latitude,
        "longitude_keluar": longitude,
        "pegawai_id": pegawaiId,
        "csh": csh,
      };

      if(state.config.ffocoa || isOnOffice(latitude, longitude)) {
        final xRequest = await http.post(
          url,
          headers: headers,
          body: jsonEncode(params),
        );

        print(xRequest.body);

        final xResponse = jsonDecode(xRequest.body);

        if (!xResponse['success']) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(xResponse['message']),
              duration: Duration(seconds: 4),
            ),
          );

          Navigator.pop(context);
        } 
        else {
          final response = await supabase
              .from('companies')
              .select()
              .eq('company_id', company.id)
              .maybeSingle();

          if (xResponse['late'] as bool) {
            await supabase.from('messages').insert({
              'receiver_id': response?['account_id'],
              'date': formatted,
              'created_at': timestamp,
              'image': uploadResponse,
              'employee_id': pegawaiId,
              'employee_name': other.namaPegawai,
              'late': xResponse['late'] as bool,
              'late_diff': xResponse['late'] as bool
                  ? xResponse['late_diff']
                  : 0,
              'action_type': 'melakukan absen pulang',
              'action_time': formattedSecond,
              'on_office': isOnOffice(latitude, longitude),
            });
          }

          ref.read(globalStateProvider.notifier).signOut(formattedTime);

          Navigator.of(context).pop();

          (() async {
            showDialog(
              context: context,
              barrierDismissible: true,
              builder: (_) => Dialog(
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(20),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        'you have been checked out',
                        style: TextStyle(fontSize: 16),
                      ),
                      const SizedBox(width: 5),
                      Icon(Icons.check_circle, color: Colors.green, size: 28),
                    ],
                  ),
                ),
              ),
            );

            await Future.delayed(Duration(seconds: 1));

            setState(() {
              latitude = latitude;
              longitude = longitude;
              path = img.path;
              preview = false;
            });

            Navigator.of(context).pop();

            await Future.delayed(Duration(seconds: 1));

            Navigator.pushNamedAndRemoveUntil(
              context,
              '/',
              (Route<dynamic> route) => false,
            );
          })();
        }
      } 
      else {
        print(isOnOffice(latitude, longitude));
        Navigator.pushNamedAndRemoveUntil(
          context,
          '/makeexception',
          (Route<dynamic> route) => false,
        );
      }
    } 
    catch (err) {
      // do something
    }
  }

  Widget setPreview() {
    return RepaintBoundary(
      key: _globalKey,
      child: Stack(
        children: [
          SizedBox(
            height: MediaQuery.of(context).size.height,
            child: Image.file(File(path)),
          ),
          Positioned(
            width: MediaQuery.of(context).size.width,
            bottom: 60,
            child: Padding(
              padding: EdgeInsets.all(16),
              child: Row(
                children: [
                  Image.network(
                    Uri.parse(Env.gStaticMap)
                        .replace(
                          queryParameters: {
                            'center': "$latitude,$longitude",
                            'size': '100x200',
                            'zoom': '18',
                            'key': Env.gMapKey,
                          },
                        )
                        .toString(),
                    fit: BoxFit.cover,
                  ),
                  SizedBox(width: 10),
                  Expanded(
                    child: Container(
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(15),
                        color: Colors.black,
                      ),
                      child: Padding(
                        padding: EdgeInsets.all(12),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              "${loc['subDistrict']}, ${loc['province']}",
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 15,
                              ),
                            ),
                            Text(
                              "${loc['address']}",
                              style: TextStyle(color: Colors.white),
                            ),
                            Text(
                              "$latitude",
                              style: TextStyle(color: Colors.white),
                            ),
                            Text(
                              "$longitude",
                              style: TextStyle(color: Colors.white),
                            ),
                            Text(
                              "${getYear(DateTime.now())} ${DateFormat('HH:mm').format(DateTime.now())}",
                              style: TextStyle(color: Colors.white),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  String setLocation(double latitude, double longitude) {
    final globalState = ref.read(globalStateProvider);
    final locations = globalState.location.list;

    for (var locs in locations) {
      final lat1 = latitude;
      final lat2 = double.parse(locs['lat']);
      final lon1 = longitude;
      final lon2 = double.parse(locs['lon']);

      if (haversineDistance(lat1, lat2, lon1, lon2) < 200) {
        return locs['locationName'];
      }
    }

    return locationName;
  }

  Widget setCamera(Map<String, dynamic> args){
    final globalState = ref.read(globalStateProvider);
    final schedule = globalState.schedule;
    final other = globalState.other;
    final workSystemName = globalState.schedule.workSystemName;
    final _workSystemName = workSystemName != "" ? workSystemName : "Day";

    return Stack(
      children: [
        Center(
          child: ClipOval(
            child: SizedBox(
              width: 300,
              height: 300,
              child: CameraPreview(_controller),
           ),
          ),
        ),
        Positioned(
          bottom: 110,
          left: 0,
          right: 0,
          child: Container(
            padding: EdgeInsets.all(12),
            margin: EdgeInsets.symmetric(horizontal: 20),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: Colors.grey.shade300),
            ),
            child: Column(
              children: [
                Row(
                  children: [
                    Icon(Icons.calendar_today, size: 18),
                    SizedBox(width: 8),
                    Text("${_workSystemName} - ${DateFormat('EEEE, dd MMM yyyy').format(DateTime.now())}"),
                  ],
                ),
                SizedBox(height: 8),
                Row(
                  children: [
                    Icon(
                        Icons.location_on,
                        size: 18,
                        color: Colors.red,
                    ),
                    SizedBox(width: 8),
                    Text(setLocation(latitude,longitude!)),
                  ],
                ),
              ],
            ),
          ),
        ),
        Positioned(
          bottom: 20,
          left: 0,
          right: 0,
          child: Container(
            margin: EdgeInsets.all(16), // margin di semua sisi
            width: double.infinity, // membuat selebar layar
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: clicked ? Colors.red : Colors.green[600],
                padding: EdgeInsets.symmetric(vertical: 16),
              ),
              onPressed: () {
                setState(() {
                    clicked = true;
                });
                captureAndUpload(
                  other.pegawaiId,
                  args['csh'],
                );
              },
              child: Text(
                "Selesai",
                 style: TextStyle(
                  color: Colors.white, // warna teks putih
                  fontSize: 16,
                ),
              ),
            ),
          ),
        ),
      ],
    );   
  }

  @override Widget build(BuildContext context) {
    final args = ModalRoute.of(context)!.settings.arguments as Map<String, dynamic>;
    final locs = ref.watch(locationProvider);
    
    return locs.when(
			loading: () => const Scaffold(
        body: Center(child: Text('please wait')),
      ),
			error: (err, _) => Scaffold(
        body: Center(child: Text('Gagal mengambil lokasi\n$err')),
      ),
			data: (position){
				WidgetsBinding.instance.addPostFrameCallback((_) {
          if(mounted){
					  setState(() {
              latitude = position.latitude;
              longitude = position.longitude;
            });
					}
        });
        return locs.when(
			    loading: () => const Scaffold(
            body: Center(child: Text('please wait')),
          ),
			    error: (err, _) => Scaffold(
            body: Center(child: Text('Gagal mengambil lokasi\n$err')),
          ),
		 	    data: (position){
				    WidgetsBinding.instance.addPostFrameCallback((_) {
              if(mounted){
					      setState(() {
                  latitude = position.latitude;
                  longitude = position.longitude;
                });
				     	}
            });

            return Scaffold(
              appBar: !preview
              ? 
              AppBar(
                  leading: IconButton(
                    icon: const Icon(Icons.arrow_back),
                    onPressed: () => Navigator.pop(context),
                  ),
                )
              : 
              null,
              body: FutureBuilder(
                future: _future,
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.done) {
                      return preview ? setPreview() : setCamera(args);
                  }  
                  else {
                    return Center(child: CircularProgressIndicator());
                  }
                },
              ),
            );
	        }
		    );
	    }
		);
  }
}
