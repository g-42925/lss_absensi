import 'dart:convert';
import 'dart:io';
import 'dart:math';
import 'dart:typed_data';
import 'dart:ui';
import 'package:camera/camera.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:geolocator/geolocator.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import '../providers/global_state.dart';
import '../env/env.dart';
import 'dart:async';
import 'package:intl/intl.dart';

class OverWorkPage extends ConsumerStatefulWidget {
  final Future<Map<String, double>> coord;
  final CameraDescription camera;

  const OverWorkPage({super.key, required this.coord, required this.camera});

  @override
  ConsumerState<OverWorkPage> createState() => _OverWorkPageState();
}

class _OverWorkPageState extends ConsumerState<OverWorkPage> {
  late Future<http.Response>? list;

  @override
  void initState() {
    super.initState();
    fetch();
  }

  Future<http.Response> getLeaveList() async {
    final globalState = ref.read(globalStateProvider);
    final other = globalState.other;
    Uri url = Uri.parse("${Env.api}/api/mobile/ovwList/${other.pegawaiId}");

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
      case "0":
        return {"status": "pending", "color": Colors.orange};
      case "1":
        return {"status": "approved", "color": Colors.green};
      default:
        return {"status": "pending", "color": Colors.orange};
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
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
                  final data = jsonDecode(response.body);
                  if (data['success'] as bool) {
                    return ListView.builder(
                      itemCount: (data['result'] as List).length,
                      itemBuilder: (context, index) {
                        final item = (data['result'] as List)[index];
                        return Card(
                          margin: EdgeInsets.only(left: 16, right: 16, top: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(6),
                          ),
                          elevation: 4,
                          child: Padding(
                            padding: EdgeInsets.all(20.0),
                            child: Row(
                              children: [
                                Expanded(
                                  child: ListTile(
                                    leading: Icon(
                                      Icons.event_note,
                                      color: getStatusColor(
                                        item['approved'],
                                      )['color'],
                                    ),
                                    title: Text(
                                      "Tgl: ${DateFormat('d MMM').format(DateTime.parse(item['date']))}",
                                      style: TextStyle(
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    subtitle: Text("Reason: ${item['reason']}"),
                                  ),
                                ),
                                SizedBox(width: 6),
                                TextButton(
                                  onPressed: () {
                                    final until = DateFormat(
                                      'yyyy-MM-dd HH:mm:ss',
                                    ).parse(item['until']);
                                    final startFrom = DateFormat(
                                      'yyyy-MM-dd HH:mm:ss',
                                    ).parse(item['start_from']);
                                    if (DateTime.now().isBefore(until)) {
                                      if (DateTime.now().isAfter(startFrom)) {
                                        if (item['approved'] == '1') {
                                          Navigator.pushNamed(
                                            context,
                                            '/overwork_start',
                                            arguments: {
                                              'id':
                                                  item['employee_overwork_detail_id'],
                                              'id2':
                                                  item['employee_overwork_id'],
                                            },
                                          );
                                        } 
                                        else {
                                          ScaffoldMessenger.of(
                                            context,
                                          ).showSnackBar(
                                            SnackBar(
                                              content: Text(
                                                "form lembur ini belum disetujui",
                                              ),
                                              duration: Duration(seconds: 4),
                                            ),
                                          );
                                        }
                                      } else {
                                        ScaffoldMessenger.of(
                                          context,
                                        ).showSnackBar(
                                          SnackBar(
                                            content: Text(
                                              "belum bisa mengakses waktu lembur",
                                            ),
                                            duration: Duration(seconds: 4),
                                          ),
                                        );
                                      }
                                    } else {
                                      ScaffoldMessenger.of(
                                        context,
                                      ).showSnackBar(
                                        SnackBar(
                                          content: Text(
                                            "form lembur ini sudah kadaluwarsa",
                                          ),
                                          duration: Duration(seconds: 4),
                                        ),
                                      );
                                    }
                                  },
                                  child: Text("Mulai"),
                                ),
                                TextButton(
                                  onPressed: () {
                                    final until = DateFormat(
                                      'yyyy-MM-dd HH:mm:ss',
                                    ).parse(item['until']);
                                    if (DateTime.now().isAfter(until)) {
                                      Navigator.pushNamed(
                                        context,
                                        '/overwork_end',
                                        arguments: {
                                          'id':
                                              item['employee_overwork_detail_id'],
                                          'id2': item['employee_overwork_id'],
                                        },
                                      );
                                    } else {
                                      ScaffoldMessenger.of(
                                        context,
                                      ).showSnackBar(
                                        SnackBar(
                                          content: Text(
                                            "form lembur ini belum bisa di klaim",
                                          ),
                                          duration: Duration(seconds: 4),
                                        ),
                                      );
                                    }
                                  },
                                  child: Text("Selesai"),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    );
                  }
                }

                return SizedBox();
              },
            )
          : SizedBox(),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.pushNamed(context, '/makeoverwork');
        },
        tooltip: 'Tambah',
        child: const Icon(Icons.add),
      ),
    );
  }
}

// class OverWorkPage extends ConsumerStatefulWidget {
//   final Future<Map<String, double>> coord;
//   final CameraDescription camera;

//   const OverWorkPage({super.key, required this.coord, required this.camera});

//   @override
//   ConsumerState<OverWorkPage> createState() => _OverWorkPageState();
// }

// class _OverWorkPageState extends ConsumerState<OverWorkPage> {
//   late CameraController _controller;
//   late Future<void> _initializeControllerFuture;
//   final GlobalKey _globalKey = GlobalKey();
//   final supabase = Supabase.instance.client;
//   bool preview = false;
//   late Future<Position>? position;

//   double latitude = 0;
//   double longitude = 0;
//   String locationName = 'Anda berada di luar area presensi';

//   String selectedValue = "";

//   Map<String, String> loc = {
//     'subDistrict': '',
//     'province': '',
//     'country': '',
//     'address': '',
//   };

//   String path = "";

//   Future<ByteBuffer> captureScreen() async {
//     await Future.delayed(Duration(milliseconds: 1000));
//     final boundary =
//         _globalKey.currentContext!.findRenderObject() as RenderRepaintBoundary;
//     final image = await boundary.toImage(pixelRatio: 3.0);
//     final byteData = await image.toByteData(format: ImageByteFormat.png);
//     return byteData?.buffer as ByteBuffer;
//   }

//   void captureAndUpload(
//     String? pegawaiId,
//     double latitude,
//     double longitude,
//   ) async {
//     final uri = Uri.parse(Env.gMapUrl).replace(
//       queryParameters: {'latlng': "$latitude,$longitude", 'key': Env.gMapKey},
//     );

//     try {
//       final now = DateTime.now();
//       final today = DateFormat('yyyy-MM-dd').format(now);
//       final img = await _controller.takePicture();
//       final requestResponse = await http.get(uri);
//       final response = jsonDecode(requestResponse.body);
//       final target = response['results'][0];
//       final addressComponents = target['address_components'];
//       final fileName = '${DateTime.now().millisecondsSinceEpoch}.png';
//       final url = Uri.parse("${Env.api}/api/mobile/overwork");
//       final headers = {"Content-type": "application/json"};
//       final globalState = ref.read(globalStateProvider);
//       final employeeId = globalState.other.pegawaiId;
//       final time = DateFormat('HH:mm:ss').format(now);

//       loc['address'] = target['formatted_address'];

//       loc['subDistrict'] = addressComponents[3]['short_name'];
//       loc['province'] = addressComponents[5]['short_name'];
//       loc['country'] = addressComponents[6]['long_name'];

//       setState(() {
//         path = img.path;
//         preview = true;
//         latitude = latitude;
//         longitude = longitude;
//       });

//       final result = await captureScreen();

//       await supabase.storage
//           .from('storage')
//           .uploadBinary(fileName, result.asUint8List());

//       final publicUrl = supabase.storage.from('storage').getPublicUrl(fileName);

//       final params = {
//         'empId': employeeId,
//         'date': today,
//         'start_from': time,
//         'until': time,
//       };

//       try {
//         await http.post(url, headers: headers, body: jsonEncode(params));

//         ref.read(globalStateProvider.notifier).overWorkStart();

//         Navigator.pushNamedAndRemoveUntil(
//           context,
//           '/',
//           (Route<dynamic> route) => false,
//         );

//         print("done");
//       } catch (e) {
//         print(e);
//       }
//     } catch (err) {
//       print("error");
//       print(err);
//     }
//   }

//   String getYear(DateTime information) {
//     return DateFormat('dd/MM/yy').format(information);
//   }

//   Widget setPreview() {
//     return RepaintBoundary(
//       key: _globalKey,
//       child: Stack(
//         children: [
//           SizedBox(
//             height: MediaQuery.of(context).size.height,
//             child: Image.file(File(path)),
//           ),
//           Positioned(
//             width: MediaQuery.of(context).size.width,
//             bottom: 60,
//             child: Padding(
//               padding: EdgeInsets.all(16),
//               child: Row(
//                 children: [
//                   Image.network(
//                     Uri.parse(Env.gStaticMap)
//                         .replace(
//                           queryParameters: {
//                             'center': "$latitude,$longitude",
//                             'size': '100x200',
//                             'zoom': '18',
//                             'key': Env.gMapKey,
//                           },
//                         )
//                         .toString(),
//                     fit: BoxFit.cover,
//                   ),
//                   SizedBox(width: 10),
//                   Expanded(
//                     child: Container(
//                       decoration: BoxDecoration(
//                         borderRadius: BorderRadius.circular(15),
//                         color: Colors.black,
//                       ),
//                       child: Padding(
//                         padding: EdgeInsets.all(12),
//                         child: Column(
//                           crossAxisAlignment: CrossAxisAlignment.start,
//                           children: [
//                             Text(
//                               "${loc['subDistrict']}, ${loc['province']}",
//                               style: TextStyle(
//                                 color: Colors.white,
//                                 fontSize: 15,
//                               ),
//                             ),
//                             Text(
//                               "${loc['address']}",
//                               style: TextStyle(color: Colors.white),
//                             ),
//                             Text(
//                               "$latitude",
//                               style: TextStyle(color: Colors.white),
//                             ),
//                             Text(
//                               "$longitude",
//                               style: TextStyle(color: Colors.white),
//                             ),
//                             Text(
//                               getYear(DateTime.now()),
//                               style: TextStyle(color: Colors.white),
//                             ),
//                           ],
//                         ),
//                       ),
//                     ),
//                   ),
//                 ],
//               ),
//             ),
//           ),
//         ],
//       ),
//     );
//   }

//   Widget setCamera(List<Map<String, dynamic>>? list, Other other) {
//     final globalState = ref.read(globalStateProvider);
//     final schedule = globalState.schedule;
//     final workSystemName = schedule.workSystemName;

//     return position != null
//         ? FutureBuilder(
//             future: position,
//             builder: (context, snapshot) {
//               if (snapshot.connectionState == ConnectionState.waiting) {
//                 return Center(child: CircularProgressIndicator());
//               }
//               if (snapshot.hasError) {
//                 WidgetsBinding.instance.addPostFrameCallback((_) {
//                   requestPositionPermission();
//                 });
//                 return Center(child: CircularProgressIndicator());
//               } else {
//                 final response = snapshot.data;
//                 final latitude = response?.latitude;
//                 final longitude = response?.longitude;

//                 return Stack(
//                   children: [
//                     Center(
//                       child: ClipOval(
//                         child: SizedBox(
//                           width: 300,
//                           height: 300,
//                           child: CameraPreview(_controller),
//                         ),
//                       ),
//                     ),
//                     Positioned(
//                       bottom: 110,
//                       left: 0,
//                       right: 0,
//                       child: Container(
//                         padding: EdgeInsets.all(12),
//                         margin: EdgeInsets.symmetric(horizontal: 20),
//                         decoration: BoxDecoration(
//                           borderRadius: BorderRadius.circular(8),
//                           border: Border.all(color: Colors.grey.shade300),
//                         ),
//                         child: Column(
//                           children: [
//                             Row(
//                               children: [
//                                 Icon(Icons.calendar_today, size: 18),
//                                 SizedBox(width: 8),
//                                 Text(
//                                   "$workSystemName - ${DateFormat('EEEE, dd MMM yyyy').format(DateTime.now())}",
//                                 ),
//                               ],
//                             ),
//                             SizedBox(height: 8),
//                             Row(
//                               children: [
//                                 Icon(
//                                   Icons.location_on,
//                                   size: 18,
//                                   color: Colors.red,
//                                 ),
//                                 SizedBox(width: 8),
//                                 Text(setLocation(latitude!, longitude!)),
//                               ],
//                             ),
//                           ],
//                         ),
//                       ),
//                     ),
//                     Positioned(
//                       bottom: 20,
//                       left: 0,
//                       right: 0,
//                       child: Container(
//                         margin: EdgeInsets.all(16), // margin di semua sisi
//                         width: double.infinity, // membuat selebar layar
//                         child: ElevatedButton(
//                           style: ElevatedButton.styleFrom(
//                             backgroundColor: Colors.green[600], // hijau gelap
//                             padding: EdgeInsets.symmetric(
//                               vertical: 16,
//                             ), // tinggi button
//                           ),
//                           onPressed: () {
//                             captureAndUpload(
//                               other.pegawaiId,
//                               latitude!,
//                               longitude!,
//                             );
//                           },
//                           child: Text(
//                             "Mulai lembur",
//                             style: TextStyle(
//                               color: Colors.white, // warna teks putih
//                               fontSize: 16,
//                             ),
//                           ),
//                         ),
//                       ),
//                     ),
//                   ],
//                 );
//               }
//             },
//           )
//         : SizedBox();
//   }

//   double toRad(double degree) {
//     return degree * pi / 180;
//   }

//   num haversineDistance(lat1, lat2, lon1, lon2) {
//     num dLat = toRad(lat2 - lat1);
//     num dLon = toRad(lon2 - lon1);
//     num cosinus1 = cos(toRad(lat1));
//     num cosinus2 = cos(toRad(lat2));
//     num cosValue = cosinus1 * cosinus2;
//     num sinus1 = pow(sin(dLat / 2), 2);
//     num sinus2 = pow(sin(dLon / 2), 2);
//     num v = sinus1 + (cosValue * sinus2);

//     double result = 6371 * (1000 * (2 * asin(sqrt(v))));

//     return double.parse(result.toStringAsFixed(2));
//   }

//   Future<void> requestPositionPermission() async {
//     setState(() {
//       position = null;
//     });

//     await Geolocator.requestPermission();

//     setState(() {
//       position = Geolocator.getCurrentPosition();
//     });
//   }

//   String setLocation(double latitude, double longitude) {
//     final globalState = ref.read(globalStateProvider);
//     final locations = globalState.location.list;

//     for (var locs in locations) {
//       final lat1 = latitude;
//       final lat2 = double.parse(locs['lat']);
//       final lon1 = longitude;
//       final lon2 = double.parse(locs['lon']);

//       if (haversineDistance(lat1, lat2, lon1, lon2) < 200) {
//         return locs['locationName'];
//       }
//     }

//     return locationName;
//   }

//   @override
//   void initState() {
//     super.initState();
//     _controller = CameraController(widget.camera, ResolutionPreset.high);
//     _initializeControllerFuture = _controller.initialize();
//     position = Geolocator.getCurrentPosition();

//     // WidgetsBinding.instance.addPostFrameCallback((_) async {
//     //   await Future.delayed(Duration(seconds: 2));
//     //   final globalState = ref.read(globalStateProvider);
//     //   final locations = globalState.location.list;
//     //   final coordinate = await widget.coord;

//     //   for (var locs in locations) {
//     //     final lat1 = coordinate['lat'];
//     //     final lat2 = double.parse(locs['lat']);
//     //     final lon1 = coordinate['lon'];
//     //     final lon2 = double.parse(locs['lon']);

//     //     if (haversineDistance(lat1, lat2, lon1, lon2) < 200) {
//     //       setState(() {
//     //         latitude = lat2;
//     //         longitude = lon2;
//     //         locationName = locs['locationName'];
//     //       });
//     //     } else {
//     //       setState(() {
//     //         latitude = lat1 as double;
//     //         longitude = lon1 as double;
//     //       });
//     //     }
//     //   }

//     //   print(latitude);
//     //   print(longitude);
//     // });
//   }

//   @override
//   Widget build(BuildContext context) {
//     final globalState = ref.read(globalStateProvider);
//     final other = globalState.other;
//     final location = globalState.location;

//     return Scaffold(
//       body: FutureBuilder(
//         future: _initializeControllerFuture,
//         builder: (context, snapshot) {
//           if (snapshot.connectionState == ConnectionState.done) {
//             return preview ? setPreview() : setCamera(location.list, other);
//           } else {
//             return Center(child: CircularProgressIndicator());
//           }
//         },
//       ),
//     );
//   }
// }
