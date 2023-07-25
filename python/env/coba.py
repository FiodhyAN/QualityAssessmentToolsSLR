import networkx as nx
import matplotlib.pyplot as plt

# Buat objek grafik berarah
G = nx.DiGraph()

# Tambahkan node ke grafik
G.add_node("Node1")
G.add_node("Node2")
G.add_node("Node3")

# Tambahkan edge (arah) ke grafik
G.add_edge("Node1", "Node2")
G.add_edge("Node2", "Node3")

# Visualisasi grafik dengan panah
pos = nx.spring_layout(G, seed=42)
nx.draw(G, pos, with_labels=True, node_size=1000, node_color="skyblue", font_size=12, font_weight="bold", arrows=True, arrowstyle="->", connectionstyle="arc3,rad=0.2")
plt.show()