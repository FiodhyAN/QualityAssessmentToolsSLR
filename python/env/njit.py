from numba import jit
import time

@jit(nopython=True)
def loop_1000():
    arr = [[0 for i in range(1000)] for j in range(1000)]
    for i in range(1000):
        for j in range(1000):
            arr[i][j] = i*j
    return arr

start_time = time.time()
arr = loop_1000()
end_time = time.time()

print(f"Program selesai dijalankan dalam waktu {end_time - start_time:.5f} detik")
