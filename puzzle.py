import time
import heapq
import random
import os

GOAL_STATE = tuple(list(range(1, 25)) + [0])
GRID_SIZE = 5

def clear_screen():
    os.system('cls' if os.name == 'nt' else 'clear')

def get_manhattan_distance(state):
    distance = 0
    for i in range(len(state)):
        val = state[i]
        if val != 0:
            curr_row, curr_col = divmod(i, GRID_SIZE)
            goal_row, goal_col = divmod(val - 1, GRID_SIZE)
            distance += abs(curr_row - goal_row) + abs(curr_col - goal_col)
    return distance

def get_neighbors(state):
    neighbors = []
    zero_idx = state.index(0)
    row, col = divmod(zero_idx, GRID_SIZE)
    
    moves = [(-1, 0, 'UP'), (1, 0, 'DOWN'), (0, -1, 'LEFT'), (0, 1, 'RIGHT')]
    
    for dr, dc, direction in moves:
        new_row, new_col = row + dr, col + dc
        if 0 <= new_row < GRID_SIZE and 0 <= new_col < GRID_SIZE:
            new_idx = new_row * GRID_SIZE + new_col
            new_state = list(state)
            new_state[zero_idx], new_state[new_idx] = new_state[new_idx], new_state[zero_idx]
            neighbors.append((tuple(new_state), direction))
    return neighbors

def generate_random_start_state(moves=40):
    state = GOAL_STATE
    for _ in range(moves):
        neighbors = get_neighbors(state)
        state, _ = random.choice(neighbors)
    return state

def print_board(state, formal_mode=False):
    if not formal_mode:
        print("-" * 23)
    for i in range(0, 25, 5):
        row = state[i:i+5]
        print(" ".join(f"{str(x).zfill(2)}" if x != 0 else "00" for x in row))
    if not formal_mode:
        print("-" * 23)
    else:
        print()

def a_star_search(start_state):
    pq = []
    h_start = get_manhattan_distance(start_state)
    heapq.heappush(pq, (h_start, 0, start_state, [], [start_state]))
    
    explored = set()
    start_time = time.time()
    
    while pq:
        f, g, current_state, moves, states_history = heapq.heappop(pq)
        
        if current_state == GOAL_STATE:
            end_time = time.time()
            return moves, states_history, (end_time - start_time)
            
        if current_state in explored:
            continue
            
        explored.add(current_state)
        
        for neighbor_state, direction in get_neighbors(current_state):
            if neighbor_state not in explored:
                new_g = g + 1
                new_h = get_manhattan_distance(neighbor_state)
                new_f = new_g + new_h
                heapq.heappush(pq, (new_f, new_g, neighbor_state, moves + [direction], states_history + [neighbor_state]))
                
    return None, None, 0

def play_game():
    print("Mengacak papan permainan....")
    current_state = generate_random_start_state(moves=30)
    initial_state = current_state # Simpan untuk direkap AI nanti
    
    while True:
        clear_screen()
        print("🎮 24-PUZZLE INTERACTIVE MODE 🎮")
        print("Gunakan W (Atas), S (Bawah), A (Kiri), D (Kanan) untuk memindahkan angka '00'.")
        print("Ketik 'AI' untuk membiarkan algoritma A* menyelesaikan puzzle ini.")
        print("Ketik 'Q' untuk keluar.\n")
        
        print_board(current_state)
        
        if current_state == GOAL_STATE:
            print("\n🎉 SELAMAT! Kamu berhasil menyelesaikannya secara manual! 🎉")
            break
            
        user_input = input("\nAksi kamu: ").strip().upper()
        
        if user_input == 'Q':
            print("Keluar dari permainan.")
            break
        elif user_input == 'AI':
            # Transisi ke formal output mode
            clear_screen()
            print("====Initial board configuration=========")
            print_board(current_state, formal_mode=True)
            print("Mencari solusi dengan A* (Manhattan Distance). Harap tunggu...")
            
            moves, history, duration = a_star_search(current_state)
            
            if moves is not None:
                clear_screen()
                print("====Initial board configuration=========")
                print_board(current_state, formal_mode=True)
                for i in range(1, len(history)):
                    print(moves[i-1])
                    print_board(history[i], formal_mode=True)
                    
                print("dst hingga ditemukan konfigurasi goal")
                print(f"Total jumlah langkah = {len(moves)}")
                print(f"Total waktu yang dibutuhkan = {duration:.4f} detik")
            else:
                print("Solusi tidak ditemukan.")
            break
            
        valid_moves = {direction: state for state, direction in get_neighbors(current_state)}
        move_map = {'W': 'UP', 'S': 'DOWN', 'A': 'LEFT', 'D': 'RIGHT'}
        
        if user_input in move_map:
            direction = move_map[user_input]
            if direction in valid_moves:
                current_state = valid_moves[direction]
            else:
                input("Mentok bro! Nggak bisa gerak ke situ. (Enter untuk lanjut)")
        else:
            input("Input nggak valid, baca instruksi woy. (Enter untuk lanjut)")

if __name__ == "__main__":
    play_game()zle