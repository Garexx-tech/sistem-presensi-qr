import os
import uuid
from pathlib import Path

import cv2
import numpy as np
from fastapi import FastAPI, File, Form, UploadFile
from fastapi.middleware.cors import CORSMiddleware
from deepface import DeepFace

app = FastAPI(title="Face Recognition Service (YOLOv8 + Facenet)")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

BASE_DIR = Path(__file__).resolve().parent
FACES_DIR = BASE_DIR / "faces"
FACES_DIR.mkdir(exist_ok=True)

DETECTOR = "yolov8n"
MODEL = os.getenv("FACE_MODEL", "Facenet")
THRESHOLD = float(os.getenv("FACE_THRESHOLD", "0.4"))


def decode_image(data: bytes) -> np.ndarray:
    arr = np.frombuffer(data, np.uint8)
    img = cv2.imdecode(arr, cv2.IMREAD_COLOR)
    if img is None:
        raise ValueError("Gambar tidak valid")
    return img


def crop_face(img: np.ndarray):
    faces = DeepFace.extract_faces(
        img_path=img, detector_backend=DETECTOR, enforce_detection=True
    )
    face = faces[0]["face"]
    if face.max() <= 1.0:
        face = (face * 255).astype(np.uint8)
    return face


@app.get("/health")
def health():
    return {"status": "ok", "detector": DETECTOR, "model": MODEL}


@app.post("/register")
def register(nis: str = Form(...), file: UploadFile = File(...)):
    nis = nis.strip()
    if not nis:
        return {"success": False, "message": "NIS wajib diisi"}

    img = decode_image(file.file.read())
    try:
        face = crop_face(img)
    except Exception:
        return {"success": False, "message": "Wajah tidak terdeteksi. Posisikan wajah di kamera."}

    person_dir = FACES_DIR / nis
    person_dir.mkdir(exist_ok=True)
    out_path = person_dir / "face.jpg"
    cv2.imwrite(str(out_path), face)

    return {"success": True, "nis": nis, "path": str(out_path)}


@app.post("/recognize")
def recognize(file: UploadFile = File(...)):
    img = decode_image(file.file.read())

    try:
        face = crop_face(img)
    except Exception:
        return {"success": False, "message": "Wajah tidak terdeteksi. Posisikan wajah di kamera."}

    tmp = Path("/tmp") / f"_tmp_{uuid.uuid4().hex}.jpg"
    cv2.imwrite(str(tmp), face)

    try:
        registered = [
            p for p in FACES_DIR.rglob("*")
            if p.is_file() and p.suffix.lower() in (".jpg", ".jpeg", ".png")
        ]
        if not registered:
            return {"success": False, "message": "Belum ada data wajah terdaftar"}

        dfs = DeepFace.find(
            img_path=str(tmp),
            db_path=str(FACES_DIR),
            model_name=MODEL,
            detector_backend=DETECTOR,
            enforce_detection=False,
            silent=True,
        )

        df = dfs[0] if isinstance(dfs, list) and len(dfs) > 0 else dfs
        if df is None or len(df) == 0:
            return {"success": False, "message": "Wajah tidak dikenali"}

        row = df.iloc[0]

        distance = None
        for col in df.columns:
            lc = col.lower()
            if any(k in lc for k in ("cosine", "distance", "euclidean")):
                distance = float(row[col])
                break
        if distance is None:
            distance = float(row[df.columns[-1]])

        identity = str(row.get("identity", ""))
        nis = Path(identity).parent.name
        confidence = round(max(0.0, 1.0 - distance), 4)

        if distance > THRESHOLD:
            return {
                "success": False,
                "nis": nis,
                "distance": distance,
                "confidence": confidence,
                "message": "Wajah tidak cocok dengan data terdaftar",
            }

        return {
            "success": True,
            "nis": nis,
            "distance": distance,
            "confidence": confidence,
            "message": "ok",
        }

    except Exception as e:
        return {"success": False, "message": f"Gagal mengenali wajah: {e}"}
    finally:
        if tmp.exists():
            tmp.unlink()